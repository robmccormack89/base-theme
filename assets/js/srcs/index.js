import UIkit from 'uikit'; // import uikit
import Icons from 'uikit/dist/js/uikit-icons'; // import uikit icons

UIkit.use(Icons); // use the Icon plugin
window.UIkit = UIkit; // Make uikit available in window for inline scripts

// import Macy from 'macy';
import * as MasonryGrid from 'macy'; // we use macy to fix bug in uk-grid: masonry
window.MasonryGrid = MasonryGrid

// import lightgallery
require('lightgallery.js');
require('lg-thumbnail.js');
require('lg-zoom.js');
require('lg-fullscreen.js');
require('lg-share.js');
require('lg-hash.js');

// import Swiper bundle with all modules installed
import Swiper from 'swiper/bundle';
window.Swiper = Swiper;

// require debounce & make available in window
window.debounce = require('debounce');

// load infinite scroll
window.InfiniteScroll = require('infinite-scroll');