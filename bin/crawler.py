#! /usr/bin/env python3
# crawler.py
# -Lee Hall Sat 27 Oct 2012 02:13:07 PM EDT

from resource import resource
from collections import deque
import psycopg2
import logging
import signal
import sys

DEBUG=True
DOMAIN="spsu.edu"
START_PAGE="http://spsu.edu/"
# DOMAIN="gtf.org"
# START_PAGE="http://minerva.gtf.org/test/"

if (DEBUG):
    logging.basicConfig(level=logging.DEBUG)
    logging.debug("Debugging enabled.")
else:
    logging.basicConfig(level=logging.INFO)

def dbclose():        
    set_term_sql="UPDATE scans SET end_time=NOW() WHERE scan_id=%s";
    cur.execute(set_term_sql,(scan_id,))
    cur.close()
    DB_Connection.close()

def sig_handler(sig, frame):
    if (sig == signal.SIGINT):
        logging.warn("Caught SIGINT. Exiting.")
        dbclose()
        sys.exit(0)
    elif (sig == signal.SIGTERM):
        logging.warn("Caught SIGTERM. Exiting.")
        dbclose()
        sys.exit(0)

signal.signal(signal.SIGINT, sig_handler)
signal.signal(signal.SIGTERM, sig_handler)

try:
    DB_Connection = psycopg2.connect("dbname=forager user=apache")
    cur=DB_Connection.cursor()
except psycopg2.Error as e:
    msg="Target Database configuration error: \"{0}{1}\".".format(type(e),e)
    logging.critical(msg)
    exit(1)

#Autocommit database queries. We don't need transactions.            
DB_Connection.set_session(autocommit=True)
create_scan_sql="""INSERT INTO scans(start_time) 
    VALUES (NOW()) RETURNING scan_id;"""
cur.execute(create_scan_sql)
scan_row=cur.fetchone()
scan_id=scan_row[0]

resource_list={}
pending=deque()
pending.append(START_PAGE)
resource_list[START_PAGE]=resource(START_PAGE,scan_id)

while (len(pending) > 0):
    logging.debug(pending)
    cur_url=pending.popleft()

    assert cur_url in resource_list, "{0} ".format(cur_url) + \
         "was placed in the pending queue, but no resource was created"
    cur_resource=resource_list[cur_url]

    assert not resource_list[cur_url].visited, \
        "Already visited resource {0} was requeued".format(cur_url)

    logging.info("Processing \"{0}\"".format(cur_url))

    cur_resource.fetch()
    #makes all data on creation
    cur_resource.Sql_Call(cur)

    for child_url in cur_resource.children:
        if (child_url in resource_list):
            logging.debug(
                "Skipping existing URL \"{0}\"".format(child_url))
            continue
        logging.debug("Queueing \"{0}\"".format(child_url))
        new_resource=resource(child_url,scan_id)
        new_resource.parent=cur_resource
        if (not new_resource.domain.endswith(DOMAIN)):
            logging.debug(
                "Skipping URL \"{0}\", outside of {1}".format(
                    child_url, DOMAIN))
            continue
        pending.append(child_url)
        resource_list[child_url]=new_resource

dbclose()
