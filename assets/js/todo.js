$(document).ready(function () {
    console.log("Document is ready");

    // Element anzeigen
    $('#addButton').click(function () {
        console.log("Add button clicked");
        $('#newTodoForm').show();
    });

    // Element verstecken und Eingabefelder zurücksetzen
    $('#cancelButton').click(function () {
        console.log("Cancel button clicked");
        $('#newTodoForm').hide();
    });
});
