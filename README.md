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
* PHP 5.6 with curl

Important: Almost all the scripts uses the private rest API to retrieve and submit data to the database (only dmnblockcomputeexpected uses direct MySQL access).

## Install:
* Go to /opt
* Get latest code from github:
```shell
git clone https://github.com/elbereth/dashninja-ctl.git
```
* Get sub-modules:
```shell
cd dashninja-ctl
git submodule update --init --recursive
```
* Configure the tool.

## Configuration:
* Copy dmn.config.inc.php.sample to dmn.config.inc.php and setup your installation.
* Add dmncron to your crontab (every minute is what official Dash Ninja uses)
```
*/1 * * * * /opt/dashninja-ctl/dmncron
```
If you want to enable logging, you need to create the /var/log/dmn/ folder and give the user write access.
Then add "log" as first argument when calling dmncron:
```
*/1 * * * * /opt/dashninja-ctl/dmncron log
```
* Add dmnthirdpartiesfetch to your crontab (every minute is fine, can be longer)
```
*/1 * * * * /opt/dashninja-ctl/dmnthirdpartiesfetch >> /dev/null
```

### dashblocknotify:
* You need /dev/shm available and writable.
* Edit dashblocknotify.config.inc.php to indicates each of your nodes you wish to retrieve block info from.
* You can either retrieve block templates (bt = true) and/or block/transaction (blocks = true). For the later you need to have txindex=1 in your dash config file.
* Add in each of your nodes in dash.conf a line to enable blocknotify feature:
```
blocknotify=/opt/dashninja-ctl/dashblocknotify
```
* Restart your node.
* On each block received by the node, the script will be called and data will be created in /dev/shm.
