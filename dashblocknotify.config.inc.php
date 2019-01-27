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
    along with Dash Ninja.  If not, see <http://www.gnu.org/licenses/>.

 */

// Indicate for each of your nodes which one you need to retrieve blocktemplate from (bt) and/or block info (block)
// Best practice for now is only retrieve block from one node and blocktemplate from all
$unamelist = array(
       'dmn01' => array('bt' => true,   'block' => false,   'mempool' => false),
       'dmn02' => array('bt' => true,   'block' => false,   'mempool' => false),
       'dmn03' => array('bt' => true,   'block' => false,   'mempool' => false),
       'p2pool' => array('bt' => true,   'block' => true,   'mempool' => true),
);

?>
