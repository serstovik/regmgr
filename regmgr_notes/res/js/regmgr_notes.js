
var RM_BACKEND_NOTES_AJAX = '/site/ajax/regmgr_notes/rmnotes';

var rmNotes	=  (function () {

	var rmNotesIsCalled = false;
	console.log(9999);	
	return function () {

		if (!rmNotesIsCalled) {
			console.log(101010);
			rmNotesIsCalled = true;

			return vEventObject([], {

				dom: {

					wrapper		: false,	//all notes wrapper
					container	: false,
					submitBtn	: false,
					editBtn		: false,
					removeBrn	: false,
					updateSection	: false,
					textArea	: false,

				},

				init: function () {

					var $this 		= this;
					$this.dom.wrapper	= jQuery("#rmnotes_list");
					$this.dom.submitBtn 	= jQuery(".regmgr-submit-note");
					$this.dom.editBtn 	= jQuery(".rmnotes-edit-btn");
					$this.dom.removeBrn 	= jQuery(".rmnotes-remove-btn");
					$this.dom.updateSection	= jQuery(".rmnotes-update-section");

					$this.dom.container 	= $this.dom.submitBtn.parents('dl');
					$this.dom.textArea 	= $this.dom.container.find('textarea');

					var addClicks	= function() {

						$this.dom.submitBtn.off().on('click.btn', function (e) {

							e.preventDefault();
							//TODO: SEND appId
							var $appId = jQuery(this).attr('rel');
							var $params = {};
							$params.appId = $appId;
							$params.text = $this.dom.textArea.val();

							mwAjax(RM_BACKEND_NOTES_AJAX + '/addNote/', $params)

								.success(function ($data) {

									// var $newEl	= $this.dom.wrapper.find('dl').last('dl').clone();

									//update element Data

									var newEl = "<dl class='mwDialog' id='" + $data.content.id + "'>";
									newEl += "<dt class='rmnotes-date'><strong>Date:</strong>'" + $data.content.modified + "'</dt>";
									newEl += "<dt class='rmnotes-text'><strong>Note:</strong>'" + $data.content.text + "'</dt>";
									newEl += "<br>";
									newEl += "<div class='rmnotes-update-section " + $data.content.id + "' style='display: none;'>";
									newEl += "<div class='rmnotes-update-section " + $data.content.id + "'>";
									newEl += "<textarea name='update_note_" + $data.content.id + "'></textarea>";
									newEl += "</div></div>";
									// newEl += "<button rel='" + $data.content.id + "' class='rmnotes-edit-btn " + $data.content.id + "'>Update</button>";
									// newEl += "<button rel='" + $data.content.id + "' class='rmnotes-remove-btn'>Remove</button>";
									newEl += "<hr></dl>";

									jQuery(newEl).appendTo($this.dom.wrapper);

									addClicks();

								}) //FUNC success

								.error(function ($data) {
									//__($data);
								}) //FUNC error

								.go();

						}); //submit click

						$this.dom.editBtn.off().on('click.editBtn', function (e) {

							e.preventDefault();
							//TODO: SEND appId
							var $noteId = jQuery(this).attr('rel');
							var $newText = $this.dom.updateSection.filter('.' + $noteId).find('textarea').val();
							var $params = {};
							$params.noteId = $noteId;
							$params.newText = $newText;

							if ($params.newText.length > 0)
								$this.dom.updateSection.hide();

							mwAjax(RM_BACKEND_NOTES_AJAX + '/updateNote/', $params)

								.success(function ($data) {

									//show up hidden textarea
									if ($data.content != undefined)
										$this.dom.updateSection.filter('.' + $data.content.id).show().find('textarea').val($data.content.text);

									//change text
									if ($newText.length > 0)
										jQuery('dl#' + $noteId).find('dt.rmnotes-text').text($newText);

								}) //FUNC success

								.error(function ($data) {
									//__($data);
								}) //FUNC error

								.go();

						}); //update click

						$this.dom.removeBrn.off().on('click.removeBtn', function (e) {

							e.preventDefault();
							//TODO: SEND appId
							var $noteId = jQuery(this).attr('rel');
							var $params = {};
							$params.noteId = $noteId;

							mwAjax(RM_BACKEND_NOTES_AJAX + '/removeNote/', $params)

								.success(function ($data) {
									//hide removed section
									jQuery('dl#' + $data.req.noteId).hide();

								}) //FUNC success

								.error(function ($data) {
									//__($data);
								}) //FUNC error

								.go();

						}); //remove click
					}

					addClicks();

				} //init

			}).init();//return
		}//if
	} //return
})();//rmNotes