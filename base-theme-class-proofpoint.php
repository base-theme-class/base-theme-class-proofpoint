<?php
/*
+----------------------------------------------------------------------
| Copyright (c) 2018,2019,2020 Genome Research Ltd.
| This is part of the Wellcome Sanger Institute extensions to
| wordpress.
+----------------------------------------------------------------------
| This extension to Worpdress is free software: you can redistribute
| it and/or modify it under the terms of the GNU Lesser General Public
| License as published by the Free Software Foundation; either version
| 3 of the License, or (at your option) any later version.
|
| This program is distributed in the hope that it will be useful, but
| WITHOUT ANY WARRANTY; without even the implied warranty of
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
| Lesser General Public License for more details.
|
| You should have received a copy of the GNU Lesser General Public
| License along with this program. If not, see:
|     <http://www.gnu.org/licenses/>.
+----------------------------------------------------------------------

# Author         : js5
# Maintainer     : js5
# Created        : 2018-02-09
# Last modified  : 2018-02-12

 * @package   BaseThemeClass/Proofpoint
 * @author    JamesSmith james@jamessmith.me.uk
 * @license   GLPL-3.0+
 * @link      https://jamessmith.me.uk/base-theme-class/proofpoint/
 * @copyright 2018 James Smith
 *
 * @wordpress-plugin
 * Plugin Name: Website Base Theme Class - Proofpoint support
 * Plugin URI:  https://jamessmith.me.uk/base-theme-class/proofpoint/
 * Description: Support functions to: tidy up fields that may
 *              contain proofpoint URL wrappers... 
 * Version:     0.1.0
 * Author:      James Smith
 * Author URI:  https://jamessmith.me.uk
 * Text Domain: base-theme-class-locale
 * License:     GNU Lesser General Public v3
 * License URI: https://www.gnu.org/licenses/lgpl.txt
 * Domain Path: /lang
*/

namespace BaseThemeClass;

class Proofpoint {
  var $self;

  function __construct( $self ) {
    $this->self = $self;
    add_action('acf/save_post', [ $this, 'proofpoint_protection_fixer' ], 5);
    return $this;
  }

  function proofpoint_protection_fixer( $post_id ) {
    $_POST['acf'] = $this->_fix_proofpoint( $_POST['acf'] );
  }

  function _fix_proofpoint($o) {
    if( is_scalar($o) ) {
      return preg_replace_callback(
        '/https:\/\/urldefense\.proofpoint\.com\/v2\/url\?u=([-.\w]*)(\&[-=;&\w]+|)/',
        function($m){
          return preg_replace_callback(
            ['/-25(60|5[CE]|7[BCD])/','/-(3[ABDF]|2[13456A89DB]|4[0]|5[BDF]|7E)/'],
            function( $matches ) {
              return chr(hexdec($matches[1]));
            },
            preg_replace(
              ['/_/','/-26quot-3B/','/-26lt-3B/','/-262339-3B/'],
              ['/','"','<',"'"],
              $m[1]
            )
          );
        },
        $o
      );
    }
    if( is_array($o) ) {
      foreach($o as $k => $v ) {
        $o[$k] = $this->_fix_proofpoint($v);
      }
    }
    return $o;
  }
}
