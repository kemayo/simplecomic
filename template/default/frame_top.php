<!DOCTYPE html5>
<html>
<head>
<title><?php echo $page->title; ?></title>
<?php $page->output_css(); ?>
<link rel="alternate" type="application/atom+xml" title="<?php echo config('title') ?> updates" href="<?php echo url('feed');?>" />
</head>
<body>