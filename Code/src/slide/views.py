from flask import Blueprint, render_template

views = Blueprint('views', __name__)


@views.route('/')
@views.route('/index')
def index():
    return 'index'


@views.route('/slides/<markdown>')
def slide(markdown):
    return render_template('slide.html', markdown=markdown)
