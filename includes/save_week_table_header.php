<?php
// +------------------------------------------------------------+
// | Copyright (c) 2008 Mark Yasuda @ Manha                     |
// +------------------------------------------------------------+
// | THIS CODE IS PROTECTED BY COPYRIGHT LAW.                   |
// |                                                            |
// | Unauthorised re-use of this code is a breach of copyright. |
// | To request the use of the code herein, please contact:     |
// |                                           ---------------- |
// |                                            info@manha.com  |
// +------------------------------------------------------------+
// | Author:  Mark Yasuda <info@manha.com>                      |
// +------------------------------------------------------------+
// | Version: 1.0                                               |
// | Created: 17/09/2008                                        |
// +------------------------------------------------------------+
// | Version History                                            |
// |                                                            |
// |  1.0 - Original version of print view header.              |
// +------------------------------------------------------------+
// | Known Bugs And Issues                                      |
// |                                                            |
// |  None.                                                     |
// +------------------------------------------------------------+


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Building Centre Diary</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
/*week view styles */
* {
	font-family:Arial,Verdana,Helvetica,sans-serif;
  padding:0px;
  font-size:10pt;
}
body {
	color:#000000;
  background:none;
  margin:0px;
}
h1 {
  font-size:18pt;
  margin-top:0px;
}
h2 {
  font-size:14pt;
  margin-top:0px;
}
hr {
  margin:15px 0px;
}
.table_data {
  width:600px;
}
.table_data .grey {
  width:200px;
}
#status {
  color:#900;
  font-weight:bold;
  margin-bottom:10px;
}
#weekview * {
  font-size:7pt;
  margin:0px;
}
#wk_title {
  font-size:12pt;
  font-weight:bold;
  margin-bottom:12px;
}
#weekview h2 {
  font-size:9pt;
  margin:2px 0px 10px;
}
#weekview .tbl_content {
  margin-bottom:15px;
}
.wk_table {
  border-collapse:collapse;
  border-spacing:0px;
}
.wk_table td , .wk_table th {
  padding:3px;
  border:solid 1px #ccc;
  vertical-align:top;
}
.nothing {
  width:678px;
}
.title {
  width:300px;
}
.ppl {
  width:50px;
  text-align:right;
}
.org {
  width:50px;
}
.loc {
  width:141px;
}
.time {
  text-align:right;
  width:108px;
}
h3 {
  font-weight:normal;
  display:inline;
}
</style>
</head>
<body>