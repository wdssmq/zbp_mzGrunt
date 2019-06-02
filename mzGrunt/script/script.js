(function() {
  let cssRefresh = function(links) {
    this.reloadFile = function(links) {
      let ve = 0;
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
              link.elem.setAttribute("href", link.href + "?x=" + Math.random());
            } else {
              location.reload();
            }
          }

        });
      }
      if (ve <= 59) {
        ve++;
      }
      setTimeout(function() {
        this.reloadFile(links);
      }, ve * 1000);
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
  cssRefresh(links);
  // console.log(getLinks());
})();
