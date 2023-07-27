#!/bin/bash
/usr/bin/rsync -a -e "ssh -i /home/mysonet/.ssh/id_rsa" mysonet@192.168.122.151:/home/mysonet/mysonet-data/ /home/mysonet/mysonet-data/
/usr/bin/rsync -a -e "ssh -i /home/mysonet/.ssh/id_rsa" mysonet@192.168.122.151:/home/mysonet/sshkey/ /home/mysonet/sshkey/

