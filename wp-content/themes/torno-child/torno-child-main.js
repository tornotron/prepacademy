function readMoreFunction(element) {
  var readMoreBtn = element;
  var productDescriptionPara = readMoreBtn.previousElementSibling;

  if (readMoreBtn.innerHTML === "read more") {
    readMoreBtn.innerHTML = "collapse"; 
    productDescriptionPara.style.maxHeight = "none";
  } else {
    readMoreBtn.innerHTML = "read more"; 
    productDescriptionPara.style.maxHeight = "8rem";
  }
}