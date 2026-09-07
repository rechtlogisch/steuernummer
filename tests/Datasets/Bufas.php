<?php

dataset('bufas-valid', [
    ['HH', '2241'],
    ['NW', '5205'],
    ['SH', '2137'],
    ['HH', '2228'],
    ['NI', '2390'],
    ['NI', '2391'],
    ['NI', '2392'],
    ['NI', '2393'],
    ['NW', '5380'],
]);

dataset('bufas-test-valid', [
    ['BB', '3098'],
    ['BE', '1197'],
    ['BW', '2866'],
    ['BY', '9198'],
    ['BY', '9296'],
    ['HB', '2497'],
    ['HE', '2653'],
    // HH has no test BUFAs
    ['MV', '4098'],
    ['NI', '2388'],
    ['NW', '5400'],
    ['NW', '5500'],
    ['NW', '5600'],
    ['RP', '2799'],
    ['SH', '2138'],
    ['SL', '1096'],
    ['SN', '3248'],
    ['ST', '3198'],
    ['TH', '4198'],
]);

dataset('bufas-invalid', [
    ['XX', '1234'],
    ['BE', '1100'],
    ['NW', '5999'],
    ['NI', '2331'], // removed in ERiC 44.2, merged into 2322 Hameln-Holzminden
    ['HE', '2612'], // removed in ERiC 44.2
    ['HE', '2613'], // removed in ERiC 44.2
    ['HE', '2615'], // removed in ERiC 44.2
    ['HE', '2617'], // removed in ERiC 44.2
    ['HE', '2635'], // removed in ERiC 44.2
    ['HE', '2643'], // removed in ERiC 44.2
    ['HE', '2647'], // removed in ERiC 44.2
]);
