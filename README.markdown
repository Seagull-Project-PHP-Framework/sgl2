# SGL2
SGL2 is a complete rewrite of the [Seagull PHP framework](http://seagullproject.org/) for PHP5.

## Installation
The easiest way to get the code up and running is to checkout `sgl2` and `sgl2_demo_app` into one directory in your web root folder.  You can alternatively place the libs in different locations, you would just modify `sgl2_demo_app/www/index.php` accordingly for the includes.

## Run the code
Run the sample project from a browser:

	http://localhost/sgl2_demo_app/www/index.php

Or commandline

	$ php sgl2_demo_app/www/index.php

## Requirements
Currently the code is working fine in >= PHP 5.2.6.

## Documentation
It's a work in progress

* http://trac.seagullproject.org/wiki/2_0/Overview

## Unit tests
All unit tests done with PHPunit, see here for instructions on setup and use:

* http://trac.seagullproject.org/wiki/2_0/UnitTests


## License

	+---------------------------------------------------------------------------+
	| Copyright (c) 2010, Demian Turner, Seagull Systems                        |
	| All rights reserved.                                                      |
	|                                                                           |
	| Redistribution and use in source and binary forms, with or without        |
	| modification, are permitted provided that the following conditions        |
	| are met:                                                                  |
	|                                                                           |
	| o Redistributions of source code must retain the above copyright          |
	|   notice, this list of conditions and the following disclaimer.           |
	| o Redistributions in binary form must reproduce the above copyright       |
	|   notice, this list of conditions and the following disclaimer in the     |
	|   documentation and/or other materials provided with the distribution.    |
	| o Neither the name of Seagull Systems nor the names of its contributors   |
	|   may be used to endorse or promote products derived from this software   |
	|   without specific prior written permission.                              |
	|                                                                           |
	| THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS       |
	| "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT         |
	| LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR     |
	| A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
	| OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,     |
	| SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT          |
	| LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,     |
	| DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
	| THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
	| (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE     |
	| OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.      |
	+---------------------------------------------------------------------------+