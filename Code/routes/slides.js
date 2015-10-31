var express = require('express');
var router = express.Router();

router.get('/:filename', function (req, res, next)
{
    res.render('slide', { markdown: req.params.filename });
});

module.exports = router;
