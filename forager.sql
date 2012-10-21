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

DROP TABLE IF EXISTS users CASCADE;
CREATE TABLE users (
	user_id 	SERIAL PRIMARY KEY,
	user_name	varchar UNIQUE NOT NULL,
	password	varchar	
);

COMMENT ON TABLE users IS 'Rudimentary user login table.';

DROP TABLE IF EXISTS scans CASCADE;
CREATE TABLE scans (
	scan_id 	SERIAL PRIMARY KEY,
	start_time	timestamp,
	end_time	timestamp	
);
COMMENT ON TABLE scans IS 'List of scans, referenced by resources';

DROP TABLE IF EXISTS resources CASCADE;
CREATE TABLE resources (
	resource_id		SERIAL PRIMARY KEY,
	scan_id			integer REFERENCES scans(scan_id)
		ON DELETE CASCADE,
	url				varchar,
	parent_id		integer REFERENCES resources(resource_id)
		ON DELETE CASCADE,
	start_date		timestamp,	
	response_time	interval,
	http_response	integer,
	UNIQUE (scan_id,url)
);

COMMENT ON TABLE resources IS 'List of pages retrieved. This forms a tree'
	' for each scan, rooted at the node with a null parent_id. This is a'
	' spanning tree of the graph in resource_children.'; 

DROP TABLE IF EXISTS resource_children CASCADE;
CREATE TABLE resource_children (
	resource_id		integer REFERENCES resources(resource_id)
		ON DELETE CASCADE,
	child_id		integer REFERENCES resources(resource_id)
		ON DELETE CASCADE
);

COMMENT ON TABLE resource_children IS 'Edge set of the graph of the website.'
	' Edges in the tree specified by parent_id also exist here.';

COMMIT;

