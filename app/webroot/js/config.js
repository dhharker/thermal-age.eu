var ADAPT_CONFIG = {
  // Where is your CSS?
  path: '/css/adapt/',

  // false = Only run once, when page first loads.
  // true = Change on window resize and page tilt.
  dynamic: true,

  // Optional callback... myCallback(i, width)
  callback: function (i, width) {
        if (i == 0)
            $('body').addClass ("mobile-layout");
        else
            $('body').removeClass ("mobile-layout");
        
    },

  // First range entry is the minimum.
  // Last range entry is the maximum.
  // Separate ranges by "to" keyword.
  /*
  range: [
    '0px    to 760px  = mobile.css',
    '760px  to 980px  = 720.css',
    '980px  to 1280px = 960.css',
    '1280px to 1600px = 1200.css',
    '1600px to 1920px = 1560.css',
    '1920px           = fluid.css'
  ]
*/
  range: [
    '0px    to 760px  = mobile.css',
    '760px  to 980px  = 720.css',
    '980px            = 960.css'
  ]
  
};
