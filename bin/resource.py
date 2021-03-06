#! /usr/bin/env python3
# resource.py
# -Lee Hall Sat 27 Oct 2012 12:21:55 PM EDT

import requests
import logging
import time
from bs4 import BeautifulSoup

DEBUG=False

class resource:
    """Represent a URL/resource."""

    DOMAIN_RESTRICTION=""
    @staticmethod
    def get_domain(url):
        method_end=url.find('://') + 3
        domain_end=url.find('/', method_end)
        return url[method_end:domain_end]

    @staticmethod
    def get_method(url):
        return url[:url.find('://') + 3]

    @staticmethod
    def set_domain(url):
        resource.DOMAIN_RESTRICTION=url

    def __init__(self, url, scan_id):
        # Directory names MUST end in a trailing space in the URL
        # URLs should start with 'http://'
        self.url=url
        self.domain=resource.get_domain(url)
        self.method=resource.get_method(url)
        self.scan_id=scan_id
        self.visited=False
        self.parent=None
        self.children=[]
        self.response_code=-1
        self.resource_id=None
        self.time_started=-1
        self.time_elapsed=-1
        self.time_start=-1

        if (DEBUG):
            logging.basicConfig(level=logging.DEBUG)

        if (resource.DOMAIN_RESTRICTION is None):
            msg="Domain restriction is unset. " + \
                "You are scanning the entire internet."
            logging.critical(msg)

    def __str__(self):
        representation = "<resource( url: {0}, domain: {1}".format(
            self.url,self.domain)
        if (self.visited):
            representation+=", response: {0}, children[".format(
                self.response_code)
            for child in self.children:
                representation += " {0} ".format(child)
            representation+="]"
        representation+=")>"
        return representation
   
    def __repr__(self):
        return "<resource: {0}>".format(self.url)

    def __eq__(self, other):
        if (type(other) is str):
            return  self.url==other
        return self.url == other.url
   
    def fetch(self):
        start=time.time()
        try:
            # Don't verify SSL connections
            r=requests.get(self.url, verify=False, timeout=5)
        except requests.Timeout:
            logging.info("Timed out fetching page {0}".format(self.url))
            self.visited=True
            self.response_code=-2
            #page timed out not 404
            return
        except requests.RequestException as e:
            logging.info("Unknown exception {0}".format(e))
            self.visited=True
            self.response_code=-3
            #dead or unreachable page not 404
            return
        except Exception as e:
            msg="Unknown, exception {0}. This is a bug in 'requests'." 
            logging.info("".format(e))
            self.response_code=-4
            return
        finally:
            elapsed=time.time()-start

        self.time_started=start
        self.time_elapsed=elapsed

        if(r is None):
            logging.warn("Request failed for {0}".format(e))
            #dead or unreachable page not 404 (should not happen)
            return
            #3 above should not happen on day to day bassis
        self.visited=True
        self.response_code=r.status_code
        self.response_time=r.headers

        # Don't parse contents of outside URLs 
        if (not self.domain.endswith(resource.DOMAIN_RESTRICTION)):
            msg="Not parsing contents of URL \"{0}\", outside of {1}"
            logging.debug(msg.format(self.url, resource.DOMAIN_RESTRICTION))
            return

        # Catch null content-type header
        if (not r.headers.get('content-type')):
            msg="No content-type header on URL \"{0}\""
            logging.debug(msg.format(self.url))
            return 

        # Only try to parse html content
        if (not r.headers.get('content-type').startswith('text/html')):
            msg="Not parsing contents of non-html URL \"{0}\""
            logging.debug(msg.format(self.url))
            return 

        self.parse_children(r)
   
    def parse_children(self, request):
        if (not self.visited):
            assert self.visited==True, "Cannot parse an unvisited page"

        try: 
            parsed=BeautifulSoup(request.text)
        except Exception as e:
            logging.warn("Exception {0} while parsing {1}".format(
                e, self.url))
            return False

        for link in parsed.find_all(['a', 'link']):
            attr=link.get('href')
            if (attr is None):
                continue
            if (attr[0:7] == "mailto:"):
                logging.info("Ignored mailto link \"{0}\" on {1}".format(
                    attr, self.url))
                continue
            if (attr[0:11] == "javascript:"):
                logging.info("Ignored javascript \"{0}\" data on {1}".format(
                    attr, self.url))
                continue
            if (attr[0:8] == "callto:"):
                logging.info("Ignored bogus URI \"{0}\" data on {1}".format(
                    attr, self.url))
                continue
            if (attr[0:4] == "tel:"):
                logging.info("Ignored bogus URI \"{0}\" data on {1}".format(
                    attr, self.url))
                continue
            self.children.append(self.canonicalize(attr))
        for link in parsed.find_all(['script', 'img']):
            attr=link.get('src')
            if (attr is None):
                continue
            if (attr[0:5] == "data:"):
                logging.info("Ignored inline image data on {0}".format(
                    self.url))
                continue
            self.children.append(self.canonicalize(attr))

    def canonicalize(self, url):
        # Absolute URL 
        # We can't deal with the last two URI schemes, but we should 
        # make sure we can record them anyway (especially file:// as this is 
        # always wrong.
        if (url.startswith('http://') or 
                url.startswith('https://') or 
                url.startswith('mms://') or
                url.startswith('file://')):
            return url
        elif (url.startswith('/')):
            can_link=self.method + self.domain + url
        else:
            can_link=self.url[:self.url.rfind('/') + 1] + url

        logging.debug("Canonicalized link {0}".format(can_link))
        return can_link

    def Sql_Call(self, connection):
        self.cur=connection
        if (self.parent is None):
            parent_id=None
        else:
            parent_id=self.parent.resource_id
        insert_sql="""
            INSERT INTO resources(scan_id,url,
                parent_id,response_time,http_response) 
            VALUES (%s,%s,%s,%s,%s) 
            RETURNING resource_id"""
        self.cur.execute(insert_sql, (self.scan_id,self.url,
            parent_id,"'{0} seconds'".format(self.time_elapsed),
            self.response_code))
        result=self.cur.fetchone() 
        self.resource_id=result[0]

    
# This only to be run when testing hte module independently
def main():
    r=resource("http://minerva.gtf.org/test/", "1")
    r.fetch()
