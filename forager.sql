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

INSERT INTO users(user_name,password) VALUES
	('test', md5('test'));


DROP TABLE IF EXISTS scans CASCADE;
CREATE TABLE scans (
	scan_id 	SERIAL PRIMARY KEY,
	pid			INTEGER,
	start_time	timestamp,
	end_time	timestamp	
);
COMMENT ON TABLE scans IS 'List of scans, referenced by resources';

INSERT INTO scans(scan_id,pid,start_time,end_time) VALUES
	(1, -1, '10/31/2012 4:00', '10/31/2012 4:30'),
	(2, -1, '10/30/2012 16:00', '10/31/2012 0:01');

SELECT setval('scans_scan_id_seq', max(scan_id)) FROM scans;


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

INSERT INTO resources(resource_id,scan_id,url,parent_id,
		start_date,response_time,http_response) VALUES
	(1, 1, 'http://minerva.gtf.org/test/', NULL, 
		'10/31/2012 4:00', '.1s', 200),
	(2, 1, 'http://minerva.gtf.org/test/index.html', 1, 
		'10/31/2012 4:01', '.1s', 200),
	(3, 1, 'http://minerva.gtf.org/test/bork.html', 1, 
		'10/31/2012 4:01', '.1s', 404);

SELECT setval('resources_resource_id_seq', max(resource_id)) FROM resources;

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

DROP TABLE IF EXISTS http_responses CASCADE;
CREATE TABLE http_responses (
	http_response		INTEGER NOT NULL,
	response_name		varchar
);

COMMENT ON TABLE http_responses IS 'Pretty lables for HTTP Response codes. '
	'Responses >0 are defined in RFC 2616, 0< are error conditions '
	'reported by the browser. We do not want inserts into the resources '
	'table to fail if there is no label, so this is NOT treated as a formal ' 
	'key.';

INSERT INTO http_responses(http_response, response_name) VALUES
	(-1, 'Unfetched resource'),
	(-2, 'Timeout'),
	(-3, 'Unknown error retrieving page'),
	(-4, 'Library failure retrieving page'),
	(200, 'OK'),
	(301, 'Moved Permanently'),
	(302, 'Found'),
	(400, 'Bad Request'),
	(401, 'Unauthorized'),
	(403, 'Forbidden'),
	(404, 'Not Found'),
	(410, 'Gone'),
	(415, 'Unsupported Media Type'),
	(500, 'Internal Server Error'),
	(503, 'Service Unavailable'),
	(504, 'Gateway Timeout'),
	(300, 'Multiple Choices'),
	(999, 'Failure reading HTTP specification');


COMMIT;

