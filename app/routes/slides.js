var express = require('express');
var router = express.Router();

router.get('/:filename', function (req, res, next)
{
    res.render('slide', { title: req.params.filename });
});

module.exports = router;
