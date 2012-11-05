#! /usr/bin/env python3
# crawler.py
# -Lee Hall Sat 27 Oct 2012 02:13:07 PM EDT

from resource import resource
from collections import deque
import psycopg2
import logging
import signal
import sys
import os

DEBUG=True
CONN_STRING="dbname=forager user=apache"
DOMAIN="spsu.edu"
START_PAGE="http://spsu.edu/"
# DOMAIN="gtf.org"
# START_PAGE="http://minerva.gtf.org/test/"

class crawler:

    def __init__(self):
        if (DEBUG):
            logging.basicConfig(level=logging.DEBUG)
            logging.debug("Debugging enabled.")
        else:
            logging.basicConfig(level=logging.INFO)
        signal.signal(signal.SIGINT, self.sig_handler)
        signal.signal(signal.SIGTERM, self.sig_handler)

        self.dbinit()
        create_scan_sql="""INSERT INTO scans(start_time) 
            VALUES (NOW()) RETURNING scan_id;"""
        self.cur.execute(create_scan_sql)
        scan_row=self.cur.fetchone()
        self.scan_id=scan_row[0]

    def __del__(self):
        self.dbclose()

    def dbclose(self):        
        set_term_sql="UPDATE scans SET end_time=NOW() WHERE scan_id=%s";
        if(hasattr(self, 'cur') and self.cur is not None):
            self.cur.execute(set_term_sql,(scan_id,))
            self.cur.close()
            self.DB_Connection.close()
        else:
            logging.warn("Crawler exited before connecting to the database.")

    def sig_handler(self,sig, frame):
        if (sig == signal.SIGINT):
            logging.warn("Caught SIGINT. Exiting.")
            dbclose()
            sys.exit(0)
        elif (sig == signal.SIGTERM):
            logging.warn("Caught SIGTERM. Exiting.")
            dbclose()
            sys.exit(0)

    def dbinit(self):

        try:
            self.DB_Connection = psycopg2.connect(CONN_STRING)
        except psycopg2.Error as e:
            msg="Target Database configuration error: \"{0}{1}\".".format(
                type(e),e)
            logging.critical(msg)
            exit(1)

        self.cur=DB_Connection.cursor()

        #Autocommit database queries. We don't need transactions.            
        DB_Connection.set_session(autocommit=True)

    def crawl(self,url):
        resource_list={}
        pending=deque()
        pending.append(url)
        resource_list[url]=resource(url,scan_id)

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
            cur_resource.Sql_Call(self.cur)

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

c=crawler()
c.crawl(START_PAGE)
