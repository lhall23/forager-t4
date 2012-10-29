#! /usr/bin/env python3
# crawler.py
# -Lee Hall Sat 27 Oct 2012 02:13:07 PM EDT

from resource import resource
from collections import deque
import logging

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

resource_list={}
pending=deque()
pending.append(START_PAGE)
resource_list[START_PAGE]=resource(START_PAGE)

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
    for child_url in cur_resource.children:
        if (child_url in resource_list):
            logging.debug(
                "Skipping existing URL \"{0}\"".format(child_url))
            continue
        logging.debug("Queueing \"{0}\"".format(child_url))
        new_resource=resource(child_url)
        new_resource.parent=cur_resource
        if (not new_resource.domain.endswith(DOMAIN)):
            logging.debug(
                "Skipping URL \"{0}\", outside of {1}".format(
                    child_url, DOMAIN))
            continue
        pending.append(child_url)
        resource_list[child_url]=new_resource
