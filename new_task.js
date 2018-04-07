function validate() {
    var ipt = $("#netID").val().length;
    if (ipt < 4) {
        alert("Please enter a NetID of at least 3 characters.");
        return false;
    }
    ipt = $("#taskDescription").val().length;
    if (ipt < 4) {
        alert("Please enter a description of at least 3 characters.");
        return false;
    }
    return true;
}