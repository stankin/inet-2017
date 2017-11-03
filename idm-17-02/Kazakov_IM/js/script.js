var tl = new TimelineMax({
  repeat: -1
});

tl.to('#cartman, #stan, #kyle', 8, {
    ease: Power0.easeIn,
    x: 1700
  }, "0")
  .to('#cartman', .2, {
    rotation: 4,
    transformOrigin: "50% 80%",
    ease: Back.easeInOut,
    repeat: 30,
    yoyo: true
  }, "0")
  .to('#stan', .2, {
    rotation: 4,
    transformOrigin: "50% 80%",
    ease: Back.easeInOut,
    repeat: 30,
    delay: .1,
    yoyo: true
  }, "0")
  .to('#kyle', .2, {
    rotation: 4,
    transformOrigin: "50% 80%",
    ease: Back.easeInOut,
    repeat: 30,
    delay: .15,
    yoyo: true
  }, "0")
  .to('#stan', .1, {
    y: 10,
    yoyo: true,
    repeat: 70,
    delay: .1
  }, "0")
  .to('#cartman', .1, {
    y: 10,
    yoyo: true,
    repeat: 73
  }, "0")
  .to('#kyle', .1, {
    y: 10,
    yoyo: true,
    repeat: 70,
    delay: .15
  }, "0")
  .to("#blink", .1, {
    opacity: 1,
    yoyo: true
  }, "3")
  .to("#blink", .1, {
    opacity: 0
  }, "3.1")
  .to("#blink", .1, {
    opacity: 1,
    yoyo: true
  }, "5")
  .to("#blink", .1, {
    opacity: 0
  }, "5.1")

