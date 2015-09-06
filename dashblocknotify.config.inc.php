<?php

/*
    This file is part of Dash Ninja.
    https://github.com/elbereth/dashninja-ctl

    Dash Ninja is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Dash Ninja is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.

 */

// Indicate for each of your nodes which one you need to retrieve blocktemplate from (bt) and/or block info (block)
// Best practice for now is only retrieve block from one node and blocktemplate from all
$unamelist = array(
       'dmn01' => array('bt' => true,   'block' => false),
       'dmn02' => array('bt' => true,   'block' => false),
       'dmn03' => array('bt' => true,   'block' => false),
       'dmn04' => array('bt' => true,   'block' => false),
       'dmn05' => array('bt' => true,   'block' => false),
       'dmn06' => array('bt' => true,   'block' => false),
       'dmn07' => array('bt' => true,   'block' => false),
       'dmn08' => array('bt' => true,   'block' => false),
       'dmn09' => array('bt' => true,   'block' => false),
       'dmn10' => array('bt' => true,   'block' => false),
       'dmn11' => array('bt' => true,   'block' => false),
       'dmn12' => array('bt' => true,   'block' => false),
       'dmn13' => array('bt' => true,   'block' => false),
       'dmn14' => array('bt' => true,   'block' => false),
       'dmn15' => array('bt' => true,   'block' => false),
       'p2pool' => array('bt' => true,   'block' => true),
       'tdmn01' => array('bt' => true,   'block' => false),
       'tdmn02' => array('bt' => true,   'block' => false),
       'tdmn03' => array('bt' => true,   'block' => false),
       'tdmn04' => array('bt' => true,   'block' => false),
       'tdmn05' => array('bt' => true,   'block' => false),
       'tdmn06' => array('bt' => true,   'block' => false),
       'tdmn07' => array('bt' => true,   'block' => false),
       'tdmn08' => array('bt' => true,   'block' => false),
       'tdmn09' => array('bt' => true,   'block' => false),
       'tdmn10' => array('bt' => true,   'block' => false),
       'tdmn11' => array('bt' => true,   'block' => false),
       'tdmn12' => array('bt' => true,   'block' => false),
       'tdmn13' => array('bt' => true,   'block' => false),
       'tdmn14' => array('bt' => true,   'block' => false),
       'tdmn15' => array('bt' => true,   'block' => false),
       'tp2pool' => array('bt' => true,   'block' => true),
);

?>
