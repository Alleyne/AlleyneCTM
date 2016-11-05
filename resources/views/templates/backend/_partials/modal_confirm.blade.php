<!-- ########################### Initialize Modal Window for confirmation ########################-->
	<div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content ui-dialog ui-corner-all ui-front ui-dialog-buttons">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h3 class="modal-title"><i class='fa fa-warning'>&nbsp</i><t>Delete Permanently</t></h3>
				</div> <!-- End Modal Header -->
				<div class="modal-body">
					<p>Are you sure about this ? </p>
				</div> <!-- End Modal Body -->
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" id="confirm"><i class='fa fa-trash-o'>&nbsp</i><x>Delete</x></button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div> <!-- End Modal Footer -->
			</div> <!-- End Modal Content -->
		</div> <!-- End Modal Dialog -->
	</div> <!-- End Modal -->