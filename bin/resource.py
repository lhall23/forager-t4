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
        strip_method=url.replace('http://','')
        return strip_method[:strip_method.find('/')]

    def __init__(self, url):
        # Directory names MUST end in a trailing space in the URL
        # URLs should start with 'http://'
        self.url=url
        self.domain=resource.get_domain(url)
        self.fetched=False
        self.children=[]
        self.response_code=-1
        self.resource_id=-1
        self.time_elapsed=-1

        if (DEBUG):
            logging.basicConfig(level=logging.DEBUG)

    #String representation is just the hashable URL
    def __str__(self):
        representation = "<resource( url: {0}, domain: {1}".format(
            self.url,self.domain)
        if (self.fetched):
            representation+=", response: {0}, childred[".format(
                self.response_code)
            for child in self.children:
                representation += " {0} ".format(child)
            representation+="]"
        representation+=")>"
        return representation
   
    def __repr__(self):
        return "<resource: {0}>".format(self.url)

    def __eq__(self, other):
        return self.url == other.url
   
    def fetch(self):
        r=requests.get(self.url)
        self.fetched=True
        self.response_code=r.status_code
        parsed=BeautifulSoup(r.text)
        for link in parsed.find_all('a'):
            can_link=self.canonicalize(link.get('href'))
            self.children.append(can_link)

    def canonicalize(self, url):
        # Absolute URL
        if (url.startswith('http://')):
            return url
        elif (url.startswith('/')):
            can_link="http://" + self.domain + url
        else:
            can_link=self.url[:self.url.rfind('/') + 1] + url

        logging.debug("Canonicalized link {0}".format(can_link))
        return can_link


# This only to be run when testing hte module independently
def main():
    r=resource("http://minerva.gtf.org/test/")
    r.fetch()
