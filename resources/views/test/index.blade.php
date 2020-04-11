@extends('layouts.main')

@section('title', 'Testscript')

@section('heading', 'Textscript')

@section('content')

    @push('scriptsHead')
        <link rel="stylesheet" href="{{ mix('/css/cards.css') }}">
    @endpush

    <div class="playingCards">

        <h1>CSS Playing Cards</h1>

        <p><strong>CSS Playing Cards help you to create simple and semantic playing card-lives in (X)HTML. This
                documents
                some examples and how to set them up.</strong></p>

        <p>Surrounding Container:</p>
        <pre>
&lt;div class="playingCards"&gt;...&lt;/div&gt;
    </pre>

        <p>With different options (default is the respective opposite):</p>
        <pre>
&lt;div class="playingCards fourColours faceImages simpleCards inText rotateHand"&gt;...&lt;/div&gt;
    </pre>

        <div class="options">
            <h3>Options:</h3>
            <ul class="toggle">
                <li title="toggles a four colour deck with a standard two colour deck">fourColours</li>
                <li title="toggles images or simple symbols for faces">faceImages</li>
                <li title="toggles the complexity of what's shown inside a card-live">simpleCards</li>
                <li title="toggles the size of the card-lives so that they can be used inside text">inText</li>
                <li title="toggles the rotation of card-lives in hand">rotateHand</li>
            </ul>
            <h3>Languages:</h3>
            <ul class="lang">
                <li title="English uses J, Q and K for Jack, Queen and King">en</li>
                <li title="German uses B, D and K for Jack, Queen and King and different colours for a four colour deck">
                    de
                </li>
                <li title="French uses V, D and R for Jack, Queen and King">fr</li>
            </ul>
        </div>

        <!-- .card-live -->
        <h2>Single Cards</h2>

        <h3>Back</h3>

        <div class="card-live back">*</div>
        <pre>
&lt;div class="card-live back"&gt;*&lt;/div&gt;
    </pre>
        <div class="clear"></div>

        <h3>Front</h3>

        <div class="card-live rank-7 spades">
            <span class="rank">7</span>
            <span class="suit">&spades;</span>
        </div>
        <pre>
&lt;div class="card-live rank-7 spades"&gt;
    &lt;span class="rank"&gt;7&lt;/span&gt;
    &lt;span class="suit"&gt;&amp;spades;&lt;/span&gt;
&lt;/div&gt;
    </pre>
        <div class="clear"></div>

        <h3>A Joker</h3>

        <div class="card-live joker little">
            <span class="rank">-</span>
            <span class="suit">Joker</span>
        </div>
        <pre>
&lt;div class="card-live little joker"&gt;
    &lt;span class="rank"&gt;-&lt;/span&gt;
    &lt;span class="suit"&gt;Joker&lt;/span&gt;
&lt;/div>
    </pre>
        <div class="clear"></div>

        <h3>Selected</h3>

        <strong>
            <span class="card-live rank-a clubs">
                <span class="rank">A</span>
                <span class="suit">&clubs;</span>
            </span>
        </strong>
        <pre>
&lt;strong&gt;
    &lt;span class="card-live rank-a clubs"&gt;
        &lt;span class="rank"&gt;A&lt;/span&gt;
        &lt;span class="suit"&gt;&amp;clubs;&lt;/span&gt;
    &lt;/span&gt;
&lt;/strong&gt;
    </pre>
        <div class="clear"></div>

        <h3>As a Link (for selecting single card-lives)</h3>

        <a class="card-live rank-q hearts" href="#">
            <span class="rank">Q</span>
            <span class="suit">&hearts;</span>
        </a>
        <pre>
&lt;a class="card-live rank-q hearts" href="#"&gt;
    &lt;span class="rank"&gt;Q&lt;/span&gt;
    &lt;span class="suit"&gt;&amp;hearts;&lt;/span&gt;
&lt;/a&gt;
    </pre>
        <div class="clear"></div>

        <h3>As a Label with Checkbox (for selecting multiple card-lives)</h3>

        <label for="c-2D" class="card-live rank-2 diams">
            <span class="rank">2</span>
            <span class="suit">&diams;</span>
            <input type="checkbox" name="c-2D" id="c-2D" value="select"/>
        </label>
        <pre>
&lt;label for="c-2D" class="card-live rank-2 diams"&gt;
    &lt;span class="rank"&gt;2&lt;/span&gt;
    &lt;span class="suit"&gt;&amp;diams;&lt;/span&gt;
    &lt;input type="checkbox" name="c-2D" id="c-2D" value="select" /&gt;
&lt;/label>
    </pre>
        <div class="clear"></div>

        <h2>Different Hands</h2>

        <!-- ul.table -->
        <h3>Lying on the Table</h3>

        <pre>
&lt;ul class="table"&gt;
    &lt;li&gt;...card-live...&lt;/li&gt;
    ...
&lt;/ul&gt;
    </pre>

        <ul class="table">
            <li>
                <a class="card-live rank-2 diams" href="#">
                    <span class="rank">2</span>
                    <span class="suit">&diams;</span>
                </a>
            </li>
            <li>
                <a class="card-live rank-q hearts" href="#">
                    <span class="rank">Q</span>
                    <span class="suit">&hearts;</span>
                </a>
            </li>
            <li>
                <a class="card-live rank-a clubs" href="#">
                    <span class="rank">A</span>
                    <span class="suit">&clubs;</span>
                </a>
            </li>
            <li>
                <a class="card-live rank-3 hearts card-selected" href="#">
                    <span class="rank">3</span>
                    <span class="suit">&hearts;</span>
                </a>
            </li>
            <li>
                <label for="c-10C" class="card-live rank-10 clubs">
                    <span class="rank">10</span>
                    <span class="suit">&clubs;</span>
                    <input type="checkbox" name="c-10C" id="c-10C" value="select"/>
                </label>
            </li>
            <li>
                <label for="c-JD" class="card-live rank-j diams">
                    <span class="rank">J</span>
                    <span class="suit">&diams;</span>
                    <input type="checkbox" name="c-JD" id="c-JD" value="select"/>
                </label>
            </li>
            <li>
                <label for="c-9S" class="card-live rank-9 spades">
                    <span class="rank">9</span>
                    <span class="suit">&spades;</span>
                    <input type="checkbox" name="c-9S" id="c-9S" value="select"/>
                </label>
            </li>
        </ul>
        <div class="clear"></div>

        <!-- ul.hand -->

        <h3>In your Hand</h3>

        <pre>
&lt;ul class="hand"&gt;
    &lt;li&gt;...card-live...&lt;/li&gt;
    ...
&lt;/ul&gt;
</pre>

        <ul class="hand">
            <li>
                <a class="card-live rank-7 diams" href="#">
                    <span class="rank">7</span>
                    <span class="suit">&diams;</span>
                </a>
            </li>
            <li>
                <a class="card-live rank-8 hearts" href="#">
                    <span class="rank">8</span>
                    <span class="suit">&hearts;</span>
                </a>
            </li>
            <li>
                <strong>
                    <a class="card-live rank-9 spades" href="#">
                        <span class="rank">9</span>
                        <span class="suit">&spades;</span>
                    </a>
                </strong>
            </li>
            <li>
                <a class="card-live rank-10 clubs" href="#">
                    <span class="rank">10</span>
                    <span class="suit">&clubs;</span>
                </a>
            </li>
            <li>
                <a class="card-live rank-j diams" href="#">
                    <span class="rank">J</span>
                    <span class="suit">&diams;</span>
                </a>
            </li>
            <li>
                <a class="card-live rank-5 clubs" href="#">
                    <span class="rank">5</span>
                    <span class="suit">&clubs;</span>
                </a>
            </li>
            <li>
                <a class="card-live rank-6 diams" href="#">
                    <span class="rank">6</span>
                    <span class="suit">&diams;</span>
                </a>
            </li>
            <li>
                <a class="card-live rank-q hearts" href="#">
                    <span class="rank">Q</span>
                    <span class="suit">&hearts;</span>
                </a>
            </li>
            <li>
                <a class="card-live rank-k spades" href="#">
                    <span class="rank">K</span>
                    <span class="suit">&spades;</span>
                </a>
            </li>
            <li>
                <a class="card-live rank-k spades" href="#">
                    <span class="rank">K</span>
                    <span class="suit">&spades;</span>
                </a>
            </li>
            <li>
                <a class="card-live rank-k spades" href="#">
                    <span class="rank">K</span>
                    <span class="suit">&spades;</span>
                </a>
            </li>
            <li>
                <a class="card-live rank-k spades" href="#">
                    <span class="rank">K</span>
                    <span class="suit">&spades;</span>
                </a>
            </li>
        </ul>
        <div class="clear"></div>

        <!-- ul.deck -->

        <h3>A Deck</h3>

        <pre>
&lt;ul class="deck"&gt;
    &lt;li&gt;...card-live...&lt;/li&gt;
    ...
&lt;/ul&gt;
</pre>

        <ul class="deck">
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
        </ul>
        <div class="clear"></div>

        <!-- A Full Set -->

        <h2 id="full">A Full Set</h2>

        <ul class="table">
            <li>
                <div class="card-live rank-2 diams">
                    <span class="rank">2</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-3 diams">
                    <span class="rank">3</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-4 diams">
                    <span class="rank">4</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-5 diams">
                    <span class="rank">5</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-6 diams">
                    <span class="rank">6</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-7 diams">
                    <span class="rank">7</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-8 diams">
                    <span class="rank">8</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-9 diams">
                    <span class="rank">9</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-10 diams">
                    <span class="rank">10</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-j diams">
                    <span class="rank">J</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-q diams">
                    <span class="rank">Q</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-k diams">
                    <span class="rank">K</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-a diams">
                    <span class="rank">A</span>
                    <span class="suit">&diams;</span>
                </div>
            </li>
        </ul>

        <ul class="table">
            <li>
                <div class="card-live rank-2 hearts">
                    <span class="rank">2</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-3 hearts">
                    <span class="rank">3</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-4 hearts">
                    <span class="rank">4</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-5 hearts">
                    <span class="rank">5</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-6 hearts">
                    <span class="rank">6</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-7 hearts">
                    <span class="rank">7</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-8 hearts">
                    <span class="rank">8</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-9 hearts">
                    <span class="rank">9</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-10 hearts">
                    <span class="rank">10</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-j hearts">
                    <span class="rank">J</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-q hearts">
                    <span class="rank">Q</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-k hearts">
                    <span class="rank">K</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-a hearts">
                    <span class="rank">A</span>
                    <span class="suit">&hearts;</span>
                </div>
            </li>
        </ul>

        <ul class="table">
            <li>
                <div class="card-live rank-2 spades">
                    <span class="rank">2</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-3 spades">
                    <span class="rank">3</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-4 spades">
                    <span class="rank">4</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-5 spades">
                    <span class="rank">5</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-6 spades">
                    <span class="rank">6</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-7 spades">
                    <span class="rank">7</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-8 spades">
                    <span class="rank">8</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-9 spades">
                    <span class="rank">9</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-10 spades">
                    <span class="rank">10</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-j spades">
                    <span class="rank">J</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-q spades">
                    <span class="rank">Q</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-k spades">
                    <span class="rank">K</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-a spades">
                    <span class="rank">A</span>
                    <span class="suit">&spades;</span>
                </div>
            </li>
        </ul>

        <ul class="table">
            <li>
                <div class="card-live rank-2 clubs">
                    <span class="rank">2</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-3 clubs">
                    <span class="rank">3</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-4 clubs">
                    <span class="rank">4</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-5 clubs">
                    <span class="rank">5</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-6 clubs">
                    <span class="rank">6</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-7 clubs">
                    <span class="rank">7</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-8 clubs">
                    <span class="rank">8</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-9 clubs">
                    <span class="rank">9</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-10 clubs">
                    <span class="rank">10</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-j clubs">
                    <span class="rank">J</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-q clubs">
                    <span class="rank">Q</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-k clubs">
                    <span class="rank">K</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
            <li>
                <div class="card-live rank-a clubs">
                    <span class="rank">A</span>
                    <span class="suit">&clubs;</span>
                </div>
            </li>
        </ul>

        <ul class="table">
            <li>
                <div class="card-live big joker">
                    <span class="rank">+</span>
                    <span class="suit">Joker</span>
                </div>
            </li>
            <li>
                <div class="card-live little joker">
                    <span class="rank">-</span>
                    <span class="suit">Joker</span>
                </div>
            </li>
            <li>
                <div class="card-live back">*</div>
            </li>
        </ul>
        <div class="clear"></div>

    </div>

@endsection
