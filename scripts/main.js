console.log("hello from main.js");

const myName = "sithara sathsarani";
console.log("the script for", myName, "is running.")

const mainHeading = document.querySelector('h1');
const aboutSection = document.querySelector('#about');

console.log(mainHeading);
console.log(aboutSection);

const themeToggleButton = document.querySelector('#theme-toggle');
const bodyElement = document.querySelector('body');


function handleThemeToggle() {
    bodyElement.classList.toggle ('dark-mode');
}
themeToggleButton.addEventListener('click',handleThemeToggle) ;   
