-- DROP, RE-CREATE AND USE THE DATABASE 
DROP DATABASE IF EXISTS blockchain;
CREATE DATABASE blockchain;
USE blockchain;

-- CREATE THE USERS TABLE
CREATE TABLE voters (
  id          BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  name        VARCHAR(128) NOT NULL,
  username    VARCHAR(64) NOT NULL,
  password    VARCHAR(64) NOT NULL,
	private_key VARCHAR(1704),
  public_key  VARCHAR(451),
	cipher      VARCHAR(100),
	iv          BLOB(16),
  is_admin    BOOLEAN NOT NULL DEFAULT false
);

-- INSERT A DEFAULT USER/VOTER AS ADMIN
INSERT INTO voters (name, username, password, is_admin) VALUE (
  'Admin', 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', TRUE
);

-- CREATE THE ELECTIONS TABLE
CREATE TABLE elections (
  id          BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  name        VARCHAR(200) NOT NULL,
  due_date    DATE NOT NULL
);

-- CREATE THE POSITIONS TABLE (Like President etc.)
CREATE TABLE positions (
	id          BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	name        VARCHAR(100) NOT NULL
);

-- CREATE THE CANDIDATES TABLE
CREATE TABLE candidates (
  id            BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  name          VARCHAR(128) NOT NULL,
  image         BLOB NOT NULL,
	election_id   BIGINT NOT NULL,
	position_id   BIGINT NOT NULL,
	FOREIGN KEY (election_id) REFERENCES elections(id),
	FOREIGN KEY (position_id) REFERENCES positions(id)
);

-- CREATE THE ELECTION SETTINGS TABLE (The settings for each election)
CREATE TABLE election_settings (
	id          BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	position_id BIGINT NOT NULL,
	election_id BIGINT NOT NULL,
	frequency   INT NOT NULL DEFAULT 1,
	FOREIGN KEY (position_id) REFERENCES positions(id),
	FOREIGN KEY (election_id) REFERENCES elections(id)
);

-- CREATE THE VOTES TABLE
-- The enrypted data should contains the voter and the voted candidate
CREATE TABLE votes (
  id           BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
  voter_id     BIGINT NOT NULL,
  candidate_id BIGINT NOT NULL,
	election_id  BIGINT NOT NULL,
	signature    VARCHAR(256) DEFAULT NULL,
  FOREIGN KEY (voter_id) REFERENCES voters(id),
  FOREIGN KEY (candidate_id) REFERENCES candidates(id),
  FOREIGN KEY (election_id) REFERENCES elections(id)
);

-----------------------------------------------------------------
--                     BLOCK CHAIN STUFF                       --
-----------------------------------------------------------------

-- This makes sure it's not connected to the votes but at the same time separated from the block chain logic through the voting server (The python server)

CREATE TABLE blocks (
	id           BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	data         BLOB NOT NULL,  -- In this should have the vote id
	nonce        INT NOT NULL,
	prev_hash    VARCHAR(256) NOT NULL,
	hash         VARCHAR(256) NOT NULL
);

CREATE TABLE blockchains (
	id           BIGINT PRIMARY KEY AUTO_INCREMENT NOT NULL,
	block_id     BIGINT NOT NULL, -- This is the head block aka starting vote
	election_id  BIGINT NOT NULL,
	difficulty   INT NOT NULL,
	FOREIGN KEY (block_id) REFERENCES blocks(id),
	FOREIGN KEY (election_id) REFERENCES elections(id)
);
