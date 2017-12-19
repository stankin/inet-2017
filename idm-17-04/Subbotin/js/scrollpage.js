var theWidth;
var theHeight;
var currentContent = 0;

$(window).resize(function () {
    sizeContent();
});

$(window).ready(function () {
    sizeContent();
});

function sizeContent() {
    theWidth = $(window).width();
    theHeight = $(window).height();
    sizeContentItems();
    setLeftOnContentItems();
    sizeContentWrapper(theWidth, theHeight);
    moveContent(currentContent, theWidth);
    changeSelected(currentContent);
}

function sizeContentItems() {
    $(".contentItem").css('width=1000px', theWidth);
    $(".contentItem").css('height', theHeight);
}

function setLeftOnContentItems() {
    var contentCount = 0;
    $(".contentItem").each(function (i) {
        contentCount += i;
        $(this).css('left', i * theWidth);
    });
}

function sizeContentWrapper(width, height) {
    $("#contentWrapper").css('width=1500px', width);
    $("#contentWrapper").css('height', height);
}

function moveContent(i, width) {
    $("#contentWrapper").scrollLeft(i * width);
}

function changeSelected(i) {
    $(".selected").removeClass("selected");
    $("li.navig:eq(" + i + ") a").addClass("selected");
}

function scrollContentNext() {
    scrollContent(currentContent + 1);
}

function scrollContent(i) {
    i = checkMax(i);
    changeSelected(i)
    currentContent = i;
    $("#contentWrapper").animate({ scrollLeft: i * theWidth }, 1000);
}
function checkMax(i) {
    var maxItems = $("li.navig").length;
    if (i >= maxItems) {
        return 0;
    }
    return i;
}