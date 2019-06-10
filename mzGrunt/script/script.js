(function() {
  let ve = 5;
  let rlt = 0;
  $(window).focus(function() {
    if (rlt > 137593) {
      cssRefresh(links);
      // location.reload(true);
    }
  });
  let cssRefresh = function(links, force = false) {
    this.reloadFile = function(links) {
      for (let a = 0, l = links.length; a < l; a++) {
        let link = links[a];
        $.get(this.getRandom(link.href), function(data, s, el) {
          // console.log(s,el);
          // console.log(el.getResponseHeader("Last-Modified"));
          let newTime = el.getResponseHeader("Last-Modified");
          if (link.last === false) {
            console.log(link.href);
            console.log(newTime);
            link.last = newTime;
            links[a] = link;
          }
          if (link.last !== newTime) {
            //	reload
            console.log(newTime);
            link.last = newTime;
            link.n++;
            if (link.n <= 3) {
              ve = 5;
              link.elem.setAttribute("href", link.href + "?x=" + Math.random());
            } else {
              location.reload(true);
            }
          }
        });
      }
      if (!force) {
        return;
      }
      if (ve <= 59) {
        ve++;
        rlt += ve * 593;
        console.log(rlt);
        setTimeout(function() {
          this.reloadFile(links);
        }, ve * 593);
      } else {
        location.reload(true);
      }
    };

    this.getRandom = function(f) {
      return f + "?x=" + Math.random();
    };

    this.reloadFile(links);
  };
  let getLinks = function() {
    var files = document.getElementsByTagName("link"),
      links = [];

    for (var a = 0, l = files.length; a < l; a++) {
      var elem = files[a],
        rel = elem.rel;

      if (typeof rel != "string" || rel.length == 0 || rel == "stylesheet") {
        links.push({
          elem: elem,
          href: elem.getAttribute("href").split("?")[0],
          last: false,
          n: 0
        });
      }
    }
    return links;
  };
  let links = getLinks();
  cssRefresh(links, true);
  // console.log(getLinks());
})();
