/*
 * forager.sql 
 * -Lee Hall Sat 20 Oct 2012 09:07:15 PM EDT
 */

/*  
 * 	If we need to drop a table, we can use a conditional drop like this:
 *  DROP TABLE IF EXISTS table_name;	
 *  And, better yet, we can put it in the transaction, so it rolls back 
 *	if things go belly up.
 */
BEGIN;

SET ROLE forager;
CREATE TABLE users (
	user_id 	SERIAL PRIMARY KEY,
	user_name	varchar UNIQUE NOT NULL,
	password	varchar	
);

CREATE TABLE scans (
	scan_id 	SERIAL PRIMARY KEY,
	start_time	timestamp,
	end_time	timestamp	
);

CREATE TABLE resources (
	resource_id		SERIAL PRIMARY KEY,
	url				varchar(),
	parent_request	integer REFERENCES resources(resource_id),
	start_date		timestamp,	
	response_time	interval,
	http_response	integer
);

CREATE TABLE resource_children (
	resource_id		integer REFERENCES resources(resource_id),
	child_id		integer REFERENCES resources(resource_id)
);

COMMIT;

