Yahoo! Updates Wordpress Plugin
==========================

Find documentation and support on Yahoo! Developer Network: http://developer.yahoo.com

 * Yahoo! Application Platform - http://developer.yahoo.com/yap/
 * Yahoo! Social APIs - http://developer.yahoo.com/social/
 * Yahoo! Query Language - http://developer.yahoo.com/yql/

Hosted on GitHub: http://github.com/yahoo/yupdates-wordpress/tree/master

License
=======

@copyright: Copyrights for code authored by Yahoo! Inc. is licensed under the following terms:
@license:   BSD Open Source License

Yahoo! Updates WordPress Plugin
Software License Agreement (BSD License)
Copyright (c) 2009, Yahoo! Inc.
All rights reserved.

Redistribution and use of this software in source and binary forms, with
or without modification, are permitted provided that the following
conditions are met:

* Redistributions of source code must retain the above
  copyright notice, this list of conditions and the
  following disclaimer.

* Redistributions in binary form must reproduce the above
  copyright notice, this list of conditions and the
  following disclaimer in the documentation and/or other
  materials provided with the distribution.

* Neither the name of Yahoo! Inc. nor the names of its
  contributors may be used to endorse or promote products
  derived from this software without specific prior
  written permission of Yahoo! Inc.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.


The Yahoo! Updates WordPress Plugin code is subject to the BSD license, see the LICENSE file.


Requirements
============
 * A self-hosted WordPress installation with the ability to install a plugin.
 * A web server running PHP 5+.
 * The Yahoo Social PHP5 SDK (yos-social-php5) - http://github.com/yahoo/yos-social-php5/


Installation
============

## Plugin Directory

* Download the plugin from the WordPress [Plugin Directory](http://wordpress.org/extend/plugins/yahoo-updates-for-wordpress/) and upload the 'yupdates-wordpress' directory to the 'wp-content/plugins/' directory of your WordPress installation on a server.
* After installation, configure the plugin according to the instructions provided in the WordPress plugin page.

## Manual Installation from GitHub

* Download and unpack the [yos-social-php5](http://github.com/yahoo/yos-social-php5/) and [yupdates-wordpress](https://github.com/yahoo/yupdates-wordpress/) source code releases. 
* After downloading and unpacking both packages, copy the contents of the SDK at 'yos-social-php5/lib'
to a new directory 'yupdates-wordpress/lib' to install the SDK. A combination install containing all required files is only available from the WordPress plugin directory. (This is our development repository)
* Upload the 'yupdates-wordpress' directory to the 'wp-content/plugins/' directory of your WordPress installation on a server.
* After installation, configure the plugin according to the instructions provided in the WordPress plugin page.