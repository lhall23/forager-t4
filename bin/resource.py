#! /usr/bin/env python3
# resource.py
# -Lee Hall Sat 27 Oct 2012 12:21:55 PM EDT

import requests
import logging
from bs4 import BeautifulSoup

DEBUG=False

class resource:
    """Represent a URL/resource."""

    @staticmethod
    def get_domain(url):
        method_end=url.find('://') + 3
        domain_end=url.find('/', method_end)
        return url[method_end:domain_end]

    def get_method(url):
        return url[:url.find('://') + 3]

    def __init__(self, url):
        # Directory names MUST end in a trailing space in the URL
        # URLs should start with 'http://'
        self.url=url
        self.domain=resource.get_domain(url)
        self.method=resource.get_method(url)
        self.visited=False
        self.parent=None
        self.children=[]
        self.response_code=-1
        self.resource_id=-1
        self.time_elapsed=-1

        if (DEBUG):
            logging.basicConfig(level=logging.DEBUG)

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
        # Don't verify SSL connections
        try:
            r=requests.get(self.url, verify=False)
        except requests.Timeout:
            logging.info("Timed out fetching page {0}".format(self.url))
            self.visited=True
            self.response_code=-1
            return
        except requests.RequestException as e:
            logging.info("Unknown exception {0}".format(e))
            self.visited=True
            self.response_code=-3
            return

        if(r is None):
            logging.warn("Request failed for {0}".format(e))
            return
            
        self.visited=True
        self.response_code=r.status_code
        
        # Only try to parse html content
        if (r.headers.get('content-type').startswith('text/html')):
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
            self.children.append(self.canonicalize(attr))
        for link in parsed.find_all(['script', 'img']):
            attr=link.get('src')
            if (attr is None):
                continue
            self.children.append(self.canonicalize(attr))

    def canonicalize(self, url):
        # Absolute URL
        if (url.startswith('http://') or url.startswith('https://')):
            return url
        elif (url.startswith('/')):
            can_link=self.method + self.domain + url
        else:
            can_link=self.url[:self.url.rfind('/') + 1] + url

        logging.debug("Canonicalized link {0}".format(can_link))
        return can_link


# This only to be run when testing hte module independently
def main():
    r=resource("http://minerva.gtf.org/test/")
    r.fetch()
