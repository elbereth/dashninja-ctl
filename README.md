# Dash Ninja Control Scripts (dashninja-ctl)
By Alexandre (aka elbereth) Devilliers

Check the running live website at https://dashninja.pl

This is part of what makes the Dash Ninja monitoring application.
It contains:
* dash-node.php : is a php implementation of Dash protocol to retrieve subver during port checking
* dashblocknotify : is the blocknotify script (for stats)
* dashblockretrieve : is a script used to retrieve block information when blocknotify script did not work (for stats)
* dashupdate : is an auto-update dashd script (uses git)
* dmnbalance : is the balance check script (for stats)
* dmnblockcomputeexpected : is a script used to compute and store the expected fields in cmd_info_blocks table
* dmnblockdegapper : is a script that detects if blocks are missing in cmd_info_blocks table and retrieve them if needed
* dmnblockparser : is the block parser script (for stats)
* dmnctl : is the control script (start, stop and status of nodes)
* dmnctlrpc : is the RPC call sub-script for the control script
* dmnctlstartstopdaemon : is the start/stop daemon sub-script for the control script
* dmncron : is the cron script
* dmnportcheck : is the port check script (for stats)
* dmnportcheckdo : is the actual port check sub-script for the port check script
* dmnreset : is the reset .dat files script
* dmnthirdpartiesfetch : is the script that fetches third party data from the web (for stats)
* dmnvotesrrd and dmnvotesrrdexport: are obsolete v11 votes storage and exported (for graphs)

## Requirement:
* Dash Ninja Back-end: https://github.com/elbereth/dashninja-be
* Dash Ninja Database: https://github.com/elbereth/dashninja-db
* Dash Ninja Front-End: https://github.com/elbereth/dashninja-fe

Important: Almost all the scripts uses the private rest API to retrieve and submit data to the database (only dmnblockcomputeexpected uses direct MySQL access).

## Install:
* Get latest code from github:
```shell
git clone https://github.com/elbereth/dashninja-ctl.git
```
* Get sub-modules.
* Configure the tool.

## Configuration:
* Copy dmn.config.inc.php.sample to dmn.config.inc.php and setup your installation.
