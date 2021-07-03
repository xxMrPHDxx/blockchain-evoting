<?php
// Get running election
$election = $conn->query("SELECT * FROM elections WHERE is_default=1");
if(!$election || $election->num_rows != 1)
	die('No election is being currently held!');
$election = $election->fetch_assoc();

// Check total votes for this user
$votes = $conn->query(
	"SELECT SUM(frequency) AS total FROM election_settings ".
	"WHERE election_id=".$election['id']
);
if(!$votes || $votes->num_rows != 1)
	die("No positions found for current election!");
$total_candidates = $votes->fetch_assoc()['total'];

// Check if user has voted
$has_voted = false;
$votes = $conn->query("SELECT * FROM votes WHERE election_id=".$election['id']);
if($votes && $votes->num_rows == $total_candidates)
	$has_voted = true;
elseif($votes && $votes->num_rows != $total_candidates)
	$conn->query(
		"DELETE FROM votes ".
		"WHERE election_id=".$election['id']." AND voter_id=".$_SESSION['id']
	);

// Get the positions/ranks along with the target count information
$positions = $conn->query(
	"SELECT *, p.id AS position_id FROM election_settings s ".
	"JOIN positions p ON s.position_id=p.id ".
	"WHERE election_id=".$election['id']
);
if(!$positions || $positions->num_rows == 0)
	die('No candidates were found!');
?>
<h1>Welcome to <?php echo $election['name'] ?></h1>
<?php if($has_voted){ ?>
<div class="app">
<?php }else{ ?>
<form id="vote" class="app" action="#">
<?php } ?>
<?php
while($position = $positions->fetch_assoc()){
	$max = $position['frequency'];
?>
<div class="position">
	<input type="hidden" name="position" value="<?php echo $position['id'] ?>" data-max="<?php echo $position['frequency'] ?>">
	<h2><?php echo $position['name'] ?></h1>
	<?php if(!$has_voted){ ?>
	<h4>Please select <?php echo $max ?> candidate<?php echo $max>1?'s':'' ?></h4>
	<?php } ?>
	<div class="candidates">
	<?php
	$candidates = $conn->query(
		"SELECT *, (SELECT COUNT(*)=1 FROM votes WHERE candidate_id=candidates.id) AS voted FROM candidates WHERE position_id=".$position['id'].
		" AND election_id=".$election['id']
	);
	while($candidate = $candidates->fetch_assoc()){
	?>
		<div class="candidate<?php if($has_voted && $candidate['voted']) echo ' selected' ?>" data-id="<?php echo $candidate['id'] ?>">
			<img src="data://image/*;base64, <?php echo $candidate['image'] ?>">
			<span><?php echo $candidate['name'] ?></span>
		</div>
	<?php
	}
	?>
	</div>
</div>
<?php
}
?>
<?php if($has_voted){ ?>
</div>
<?php }else{ ?>
	<input type="submit" id="confirm" value="Confirm Vote">
</form>
<?php } ?>
<?php if(!$has_voted){ ?>
<script type="text/javascript" defer>
$('.position').each((i,p)=>{
	const pp = $($(p).find('input[name=position]')[0]);
	const pos = parseInt(pp.attr('value'));
	const max = parseInt(pp.attr('data-max'));
	$(p).find('.candidate').each((j,c)=>{
		$(c).click(e=>{
			const off = $(c).hasClass('selected') ? -1 : 1;
			const count = $(p).find('.candidate.selected').length+off;
			if(count <= max) $(c).toggleClass('selected');
		});
	});
});
$('#vote').submit(e=>{
	e.preventDefault();
	let error = null;
	$('.position').each((i,p)=>{
		const sel = $(p).find('.candidate.selected').length;
		const tar = parseInt($(p).find('[name=position]').attr('data-max'));
		if(sel !== tar){
			error = true;
			const h4 = $(p).find('h4').first();
			h4.addClass('error');
			h4[0].scrollIntoView();
			setTimeout(()=>h4.removeClass('error'), 3000);
		}
	});
	Promise.all([...$('.candidates .candidate.selected')].map(e=>
		fetch(`ajax.php?action=vote&candidate=${$(e).attr('data-id')}&election=<?php echo $election['id'] ?>`, {
			method: 'POST',
			headers: {
				Authorization: btoa(`<?php echo $_SESSION['public_key'] ?>`)
			},
			body: (data=>{
				data.append('candidate', $(e).attr('data-id'));
				data.append('election', <?php echo $election['id'] ?>);
				return data;
			})(new FormData())
		})
		.then(res=>res.json())
	))
	.then(results=>Promise.all(results.filter(({success})=>success).map(({vote_id})=>
		fetch(`http://localhost:8000/vote?vote=${vote_id}&election=<?php echo $election['id'] ?>`, {
			headers: {
				Authorization: btoa(`<?php echo $_SESSION['public_key'] ?>`)
			}
		})
		.then(res=>res.json())
	)))
	.then(results=>results.filter(({success})=>!success))
	.then(errors=>{
		if(errors.length === 0){
			alert('Vote has been submitted successfully!');
			$('#confirm').remove();
			setTimeout(()=>location.reload(), 1500);
		}
	})
	.catch(e=>alert(`Error: Failed to submit vote! ${e}`));
});
</script>
<?php } ?>
