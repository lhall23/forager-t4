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
LOGFILE="/var/log/forager.log"

# DOMAIN="gtf.org"
# START_PAGE="http://minerva.gtf.org/test/"

class crawler:

    def __init__(self):
        if (DEBUG):
            logging.basicConfig(level=logging.DEBUG, filename=LOGFILE)
            logging.debug("Debugging enabled.")
        else:
            logging.basicConfig(level=logging.INFO)
        signal.signal(signal.SIGINT, self.sig_handler)
        signal.signal(signal.SIGTERM, self.sig_handler)
        
        self.daemonize()
        self.dbinit()

    def dbclose(self):        
        set_term_sql="UPDATE scans SET end_time=NOW() WHERE scan_id=%s";
        if(hasattr(self, 'cur') and self.cur is not None):
            self.cur.execute(set_term_sql,(self.scan_id,))
            self.cur.close()
            self.DB_Connection.close()
        else:
            logging.warn("Crawler exited before connecting to the database.")

    def sig_handler(self,sig, frame):
        if (sig == signal.SIGINT):
            logging.warn("Caught SIGINT. Exiting.")
            self.dbclose()
            sys.exit(0)
        elif (sig == signal.SIGTERM):
            logging.warn("Caught SIGTERM. Exiting.")
            self.dbclose()
            sys.exit(0)

    def dbinit(self):

        try:
            self.DB_Connection = psycopg2.connect(CONN_STRING)
        except psycopg2.Error as e:
            msg="Target Database configuration error: \"{0}{1}\".".format(
                type(e),e)
            logging.critical(msg)
            exit(1)

        self.cur=self.DB_Connection.cursor()

        #Autocommit database queries. We don't need transactions.            
        self.DB_Connection.set_session(autocommit=True)

    # Daemonize crawler process. This is adapted from Stevens's Advanced
    # Programming in a Unix Environment, and ported to python3 by an anonymous
    # user. Source is available here: http://www.jejik.com/files/examples/daemon3x.py
    # Stevens's original code starts on page 426 in the second edition, (c) 1995.
    def daemonize(self):
        #FOrk
        try: 
            pid= os.fork()
            if (pid > 0):
                sys.exit(0)
        except OSError as e:
            logging.warn("Fork failed: {0}.".format(e))
            sys.exit(1)

        logging.info("Forked as.".format(pid))
        # Reset env
        os.chdir('/')
        os.setsid()
        os.umask(0)

        #Fork again.
        try: 
            pid= os.fork()
            if (pid > 0):
                sys.exit(0)
        except OSError as e:
            logging.warn("Fork failed: {0}.".format(e))
            sys.exit(1)
        logging.info("Forked again.".format(pid))

        sys.stdout.flush()
        sys.stderr.flush()


        # Open devnull and move input/output over there.
        si=open(os.devnull, 'r')
        so=open(os.devnull, 'a+')
        se=open(os.devnull, 'a+')

        os.dup2(si.fileno(), sys.stdin.fileno())
        os.dup2(so.fileno(), sys.stdout.fileno())
        os.dup2(se.fileno(), sys.stderr.fileno())

        logging.info("Detatched from terminal.".format(pid))
       
         

    def crawl(self,url):
        logging.info("Starting crawl at {0}.".format(url))
        pid=os.getpid()
        create_scan_sql="""INSERT INTO scans(start_time,pid) 
            VALUES (NOW(), %s) RETURNING scan_id;"""
        self.cur.execute(create_scan_sql, (pid,))
        scan_row=self.cur.fetchone()
        self.scan_id=scan_row[0]
        resource_list={}
        pending=deque()
        pending.append(url)
        resource_list[url]=resource(url,self.scan_id)

        while (len(pending) > 0):
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
                new_resource=resource(child_url,self.scan_id)
                new_resource.parent=cur_resource
                if (not new_resource.domain.endswith(DOMAIN)):
                    logging.debug(
                        "Skipping URL \"{0}\", outside of {1}".format(
                            child_url, DOMAIN))
                    continue
                pending.append(child_url)
                resource_list[child_url]=new_resource
        logging.info("All queued items have been scanned.");

try:
    c=crawler()
    c.crawl(START_PAGE)
    c.dbclose()
except Exception as e:
    logging.critical("Something exploded: {0}".format(e))
