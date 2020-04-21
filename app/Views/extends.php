<?php use App\Facades\V; ?>

<?php V::extends('part'); ?>

<?php V::section('title', 'Title'); ?>

<?php V::section('part'); ?>
<div>Section</div>
<?php $request = V::inject('request'); ?>
<div><?php echo $request->path(); ?></div>
<?php V::endsection(); ?>
