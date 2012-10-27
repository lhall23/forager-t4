#! /usr/bin/env python3
# crawler.py
# -Lee Hall Sat 27 Oct 2012 02:13:07 PM EDT

from resource import resource
from collections import deque
import logging

DEBUG=False
DOMAIN="gtf.org"

if (DEBUG):
    logging.basicConfig(level=logging.DEBUG)
    logging.debug("Debugging enabled.")
else:
    logging.basicConfig(level=logging.INFO)

visited={}
pending=deque()
pending.append(resource("http://minerva.gtf.org/test/"))
while (len(pending) > 0):
    logging.debug(pending)
    cur_resource=pending.popleft()

    if (cur_resource.url in visited):
        logging.debug(
            "Skipping already fetched URL \"{0}\"".format(cur_resource.url))
        continue

    logging.info("Processing \"{0}\"".format(cur_resource.url))

    visited[cur_resource.url]=cur_resource
    cur_resource.fetch()
    print(cur_resource.children)
    for child in cur_resource.children:
        if (pending.count(child) > 0):
            logging.debug(
                "Skipping already queued URL \"{0}\"".format(cur_resource.url))
            continue
        logging.debug("Queueing \"{0}\"".format(child))
        new_resource=resource(child)
        if (not new_resource.domain.endswith(DOMAIN)):
            logging.debug(
                "Skipping URL \"{0}\", outside of {1}".format(
                    cur_resource.url, DOMAIN))
            continue
        pending.append(new_resource)
