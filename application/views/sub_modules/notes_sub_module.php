<hr>

<div class="well">

	<div class="row">

		<div class="append-icon col-sm-6">

			<div class="form-group">

				<label for="note">Note</label>

				<a href="" data-toggle="modal" data-target="#note_template_modal" class="pull-right">+ Add Template</a>
 
				<textarea class="form-control" name="note1" id="note1"><?=(@$data ? str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "&#10;", $data[0]->note1) : '');?></textarea>

			</div>

		</div>

		<div class="append-icon col-sm-6">

			<div class="form-group">

				<label for="note">Note</label>

				<a href="" data-toggle="modal" data-target="#note_template_modal" class="pull-right">+ Add Template</a>

				<textarea class="form-control" name="note2" id="note2"><?=(@$data ? str_replace(array("\r\n", "\\r\\n", "\n", "\\n"), "&#10;", $data[0]->note2) : '');?></textarea>

			</div>

			<span class="validation-color" id="err_details"></span>

		</div>

	</div>

</div>



<?php $this -> load -> view('note_template/note_template_modal.php'); ?>

<script src="<?php echo base_url('assets/js/note_template/') ?>note.js"></script>

