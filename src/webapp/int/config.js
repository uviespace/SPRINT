var url = "http://localhost/SPRINT/";

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
  scrollFunction();
};

function scrollFunction() {
  //Get the button
  let mybutton = document.getElementById("btn-back-to-top");
  if (mybutton != null) {
    // When the user clicks on the button, scroll to the top of the document
    mybutton.addEventListener("click", backToTop);
    if (
      document.body.scrollTop > 20 ||
      document.documentElement.scrollTop > 20
    ) {
      mybutton.style.display = "block";
    } else {
      mybutton.style.display = "none";
    }
  }
}

function backToTop() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}
