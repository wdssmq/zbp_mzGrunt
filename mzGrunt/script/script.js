(function () {
  const vve = 37;
  let ve = vve;
  $(window).focus(function () {
    cssRefresh(links, true);
  });
  /**
   *
   * @param array links 样式链接列表
   * @param bool  sp 强制刷新一次但不设置定时
   */
  const cssRefresh = function (links, sp = false) {
    this.reloadFile = function (links) {
      for (let a = 0, l = links.length; a < l; a++) {
        const link = links[a];
        $.get(this.getRandom(link.href), function (data, s, el) {
          // console.log(s,el);
          // console.log(el.getResponseHeader("Last-Modified"));
          const newTime = el.getResponseHeader("Last-Modified");
          // 初始化时间
          if (link.last === false) {
            link.last = newTime;
            console.log("初始时间");
            console.log(link.href);
            console.log(link.last);
            links[a] = link;
          } else {
            console.log("查询中", link.href);
          }
          // 时间不一致时刷新
          if (link.last !== newTime) {
            // reload
            console.log(link.last, newTime);
            link.last = newTime;
            link.n++;
            if (link.n <= 7) {
              ve = vve;
              link.elem.setAttribute("href", cssRefresh.getRandom(link.href));
            } else {
              location.reload(true);
            }
          }
        });
      }
      if (sp) {
        console.log(ve, sp);
        sp = false;
        return;
      }
      if (ve <= 59) {
        ve++;
        let delay = ve * 1000 + 137;
        console.log(delay);
        setTimeout(function () {
          this.reloadFile(links);
        }, delay);
      } else {
        location.reload(true);
      }
    };

    this.getRandom = function (f) {
      return f + "?x=" + Math.random();
    };

    this.reloadFile(links);
  };
  // 遍历取得所有外部样式链接
  const getLinks = function () {
    const files = document.getElementsByTagName("link"),
      links = [];
    for (let a = 0, l = files.length; a < l; a++) {
      const elem = files[a],
        rel = elem.rel;
      if (typeof rel != "string" || rel.length == 0 || rel == "stylesheet") {
        links.push({
          elem: elem,
          href: elem.getAttribute("href").split("?")[0],
          last: false,
          n: 0,
        });
      }
    }
    return links;
  };
  const links = getLinks();
  cssRefresh(links);
  // console.log(getLinks());
})();
