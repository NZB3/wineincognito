$(function(){
  $(document).on("change",".edit-user input.employment",function(){
    var $editUser = $(this).closest(".edit-user");
    var employment = $editUser.find("input.employment:checked").first().val();
    togglePlaceOfWork($editUser,employment);
  });
  function togglePlaceOfWork($editUser,employment){
    var $placeOfWork = $editUser.find("tr.place-of-work");
    if(employment==2){
      $placeOfWork.show();
    } else {
      $placeOfWork.hide();
    }
  }
  $(".edit-user input.employment").closest(".edit-user").each(function(){
    var $editUser = $(this);
    var employment = $editUser.find("input.employment:checked").first().val();
    togglePlaceOfWork($editUser,employment);
  });
});