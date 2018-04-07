#!/bin/bash
find /alidata/log/nginx/access/ -mtime +7 -type f -name *.log | xargs rm -f
