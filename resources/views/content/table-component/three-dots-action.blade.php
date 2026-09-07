<style>
    .drop-navigation {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .drop-menuToggle {
        position: relative;
        width: 30px;
        height: 30px;
        background: #fff;
        border-radius: 70px;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
    }

    .drop-menuToggle::before {
        content: '+';
        position: absolute;
        font-size: 1.5em;
        font-weight: 200;
        color: #ff216d;
        transition: 1.5s;
    }

    .drop-menuToggle.active::before {
        transform: rotate(225deg);
    }

    .drop-menu {
        position: absolute;
        width: 30px;
        height: 30px;
        background: white;
        border-radius: 70px;
        z-index: -1;
        transition: transform 0.5s, width 0.5s, height 0.5s;
        transition-delay: 0.5s, 0.5s, 0.5s;
        transition-timing-function: cubic-bezier(0.075, 0.82, 0.165, 1);
    }

    .drop-menuToggle.active~.drop-menu {
        width: 170px;
        height: 45px;
        z-index: 10000;
        transform: translate(112px);
        transition-delay: 0s, 0.2s, 0.2s;
        box-shadow: 0 15px 25px rgb(78 43 43 / 10%);
    }

    .drop-menu::before {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        background: #fff;
        left: -5px;
        bottom: 4px;
        transform: rotate(45deg);
        border-radius: 2px;
        /* transition: 0.5s; */
        top: 40%;
    }

    .drop-menuToggle.active~.drop-menu::before {
        transition-delay: 0.5s;
        bottom: -6px;
    }

    .drop-menu ul {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        height: 45px;
        /* gap: 40px; */
        padding: 0;
    }

    .drop-menu ul li {
        list-style: none;
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-30px);
        transition: 0.25s;
        transition-delay: calc(0s + var(--i));
    }

    .drop-menuToggle.active~.drop-menu ul li {
        opacity: 1;
        visibility: visible;
        transform: translateY(0px);
        transition-delay: calc(0.2s + var(--i));
    }

    .drop-menu ul li a {
        display: block;
        font-size: 3em;
        text-decoration: none;
        color: #555;
    }

    .drop-menu ul li a:hover {
        color: #ff216d !important;
        cursor: pointer;
    }

</style>


<div class="drop-navigation">
    <!-- Menu Toggler -->
    <div class="drop-menuToggle"></div>
    <!-- Menu Items -->
    <div class="drop-menu">
        <ul>
            {{-- <li style="--i:0.1s;"><button class="btn btn-icon rounded-circle btn-danger waves-effect">
                    <span><svg aria-hidden="true" focusable="false" data-prefix="fa-duotone" data-icon="trash-can" class="svg-inline--fa fa-trash-can fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <defs>
                                <style>
                                    .fa-secondary {
                                        opacity: .4
                                    }

                                </style>
                            </defs>
                            <g class="fa-group">
                                <path class="fa-primary" d="M432 32H320l-11.58-23.16c-2.709-5.42-8.25-8.844-14.31-8.844H153.9c-6.061 0-11.6 3.424-14.31 8.844L128 32H16c-8.836 0-16 7.162-16 16V80c0 8.836 7.164 16 16 16h416c8.838 0 16-7.164 16-16V48C448 39.16 440.8 32 432 32z" fill="currentColor" />
                                <path class="fa-secondary" d="M32 96v368C32 490.5 53.5 512 80 512h288c26.5 0 48-21.5 48-48V96H32zM144 416c0 8.875-7.125 16-16 16S112 424.9 112 416V192c0-8.875 7.125-16 16-16S144 183.1 144 192V416zM240 416c0 8.875-7.125 16-16 16S208 424.9 208 416V192c0-8.875 7.125-16 16-16s16 7.125 16 16V416zM336 416c0 8.875-7.125 16-16 16s-16-7.125-16-16V192c0-8.875 7.125-16 16-16s16 7.125 16 16V416z" fill="currentColor" />
                            </g>
                        </svg></span>
                </button>
                <button class="btn btn-icon rounded-circle btn-danger waves-effect">
                    <span><svg aria-hidden="true" focusable="false" data-prefix="fa-duotone" data-icon="trash-can" class="svg-inline--fa fa-trash-can fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <defs>
                                <style>
                                    .fa-secondary {
                                        opacity: .4
                                    }

                                </style>
                            </defs>
                            <g class="fa-group">
                                <path class="fa-primary" d="M432 32H320l-11.58-23.16c-2.709-5.42-8.25-8.844-14.31-8.844H153.9c-6.061 0-11.6 3.424-14.31 8.844L128 32H16c-8.836 0-16 7.162-16 16V80c0 8.836 7.164 16 16 16h416c8.838 0 16-7.164 16-16V48C448 39.16 440.8 32 432 32z" fill="currentColor" />
                                <path class="fa-secondary" d="M32 96v368C32 490.5 53.5 512 80 512h288c26.5 0 48-21.5 48-48V96H32zM144 416c0 8.875-7.125 16-16 16S112 424.9 112 416V192c0-8.875 7.125-16 16-16S144 183.1 144 192V416zM240 416c0 8.875-7.125 16-16 16S208 424.9 208 416V192c0-8.875 7.125-16 16-16s16 7.125 16 16V416zM336 416c0 8.875-7.125 16-16 16s-16-7.125-16-16V192c0-8.875 7.125-16 16-16s16 7.125 16 16V416z" fill="currentColor" />
                            </g>
                        </svg></span>
                </button>
            </li> --}}
            <li style="--i:0.1s;">
                @php
                    if(empty($edit_route)){
                        $edit_route = '';
                        $edit_callback = '';
                        $modal = '';
                    }
                    if(empty($delete_route)){
                        $delete_route = '';
                    }
                    if(empty($custom)){
                        $custom = '';
                    }
                    $button = view('content.table-component.action', compact('edit_route', 'delete_route', 'edit_callback', 'modal', 'custom'));
                @endphp
                {{ $button }}
            </li>
            {{-- <li style="--i:0.1s;"><svg width="60%" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11.76 11.76">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: red;
                                        isolation: isolate;
                                        opacity: 0.45;
                                    }

                                    .cls-2 {
                                        fill: #818587;
                                    }

                                </style>
                            </defs>
                            <g id="Layer_2" data-name="Layer 2">
                                <g id="Layer_1-2" data-name="Layer 1">
                                    <circle class="cls-1" cx="5.88" cy="5.88" r="5.88" />
                                    <polygon points="5.89 4.61 3.73 4.61 4.73 9.32 5.89 9.32 7.06 9.32 8.06 4.61 5.89 4.61" />
                                    <path d="M8,4.13,6.61,3.44h0l.07-.14a.15.15,0,0,0-.06-.19l-.36-.18A.15.15,0,0,0,6.07,3L6,3.13v.05L4.65,2.49a.15.15,0,0,0-.19.06v.06a.14.14,0,0,0,0,.18h0L7.81,4.47A.14.14,0,0,0,8,4.41H8V4.35a.14.14,0,0,0,0-.2Z" />
                                    <path d="M3.7,4.17a1.58,1.58,0,0,1,.55-.51.85.85,0,0,1,.79,0,1.55,1.55,0,0,1,.39.28H5.09l.83.35-.4-.93V3.8a1.52,1.52,0,0,0-.6-.32.89.89,0,0,0-1,.34A1.15,1.15,0,0,0,3.7,4.17Z" />
                                    <polygon class="cls-2" points="4.41 5.12 5.41 5.12 5.41 8.8 4.96 8.8 4.31 5.12 4.41 5.12" />
                                    <polygon class="cls-2" points="7.38 5.12 6.37 5.12 6.37 8.8 6.83 8.8 7.47 5.12 7.38 5.12" />
                                    <rect class="cls-2" x="5.53" y="5.12" width="0.71" height="3.68" />
                                </g>
                            </g>
                        </svg>
            </li>
            <li style="--i:0.1s;"><a href="#">
                    <span class="d-inline-block" style="width:40px"> <svg width="100%" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11.76 11.76">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: red;
                                        isolation: isolate;
                                        opacity: 0.45;
                                    }

                                    .cls-2 {
                                        fill: #818587;
                                    }

                                </style>
                            </defs>
                            <g id="Layer_2" data-name="Layer 2">
                                <g id="Layer_1-2" data-name="Layer 1">
                                    <circle class="cls-1" cx="5.88" cy="5.88" r="5.88" />
                                    <polygon points="5.89 4.61 3.73 4.61 4.73 9.32 5.89 9.32 7.06 9.32 8.06 4.61 5.89 4.61" />
                                    <path d="M8,4.13,6.61,3.44h0l.07-.14a.15.15,0,0,0-.06-.19l-.36-.18A.15.15,0,0,0,6.07,3L6,3.13v.05L4.65,2.49a.15.15,0,0,0-.19.06v.06a.14.14,0,0,0,0,.18h0L7.81,4.47A.14.14,0,0,0,8,4.41H8V4.35a.14.14,0,0,0,0-.2Z" />
                                    <path d="M3.7,4.17a1.58,1.58,0,0,1,.55-.51.85.85,0,0,1,.79,0,1.55,1.55,0,0,1,.39.28H5.09l.83.35-.4-.93V3.8a1.52,1.52,0,0,0-.6-.32.89.89,0,0,0-1,.34A1.15,1.15,0,0,0,3.7,4.17Z" />
                                    <polygon class="cls-2" points="4.41 5.12 5.41 5.12 5.41 8.8 4.96 8.8 4.31 5.12 4.41 5.12" />
                                    <polygon class="cls-2" points="7.38 5.12 6.37 5.12 6.37 8.8 6.83 8.8 7.47 5.12 7.38 5.12" />
                                    <rect class="cls-2" x="5.53" y="5.12" width="0.71" height="3.68" />
                                </g>
                            </g>
                        </svg></span>
                </a>
            </li> --}}
            {{-- <li style="--i:0.1s;"><a href="#">
                    <span class="d-inline-block" style="width:50%"> <svg width="100%"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11.76 11.76">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: red;
                                        isolation: isolate;
                                        opacity: 0.45;
                                    }

                                    .cls-2 {
                                        fill: #818587;
                                    }

                                </style>
                            </defs>
                            <g id="Layer_2" data-name="Layer 2">
                                <g id="Layer_1-2" data-name="Layer 1">
                                    <circle class="cls-1" cx="5.88" cy="5.88" r="5.88" />
                                    <polygon points="5.89 4.61 3.73 4.61 4.73 9.32 5.89 9.32 7.06 9.32 8.06 4.61 5.89 4.61" />
                                    <path d="M8,4.13,6.61,3.44h0l.07-.14a.15.15,0,0,0-.06-.19l-.36-.18A.15.15,0,0,0,6.07,3L6,3.13v.05L4.65,2.49a.15.15,0,0,0-.19.06v.06a.14.14,0,0,0,0,.18h0L7.81,4.47A.14.14,0,0,0,8,4.41H8V4.35a.14.14,0,0,0,0-.2Z" />
                                    <path d="M3.7,4.17a1.58,1.58,0,0,1,.55-.51.85.85,0,0,1,.79,0,1.55,1.55,0,0,1,.39.28H5.09l.83.35-.4-.93V3.8a1.52,1.52,0,0,0-.6-.32.89.89,0,0,0-1,.34A1.15,1.15,0,0,0,3.7,4.17Z" />
                                    <polygon class="cls-2" points="4.41 5.12 5.41 5.12 5.41 8.8 4.96 8.8 4.31 5.12 4.41 5.12" />
                                    <polygon class="cls-2" points="7.38 5.12 6.37 5.12 6.37 8.8 6.83 8.8 7.47 5.12 7.38 5.12" />
                                    <rect class="cls-2" x="5.53" y="5.12" width="0.71" height="3.68" />
                                </g>
                            </g>
                        </svg></span>
                </a>
            </li> --}}
            {{-- <li style="--i:0.1s;"><a href="#">
                    <span class="d-inline-block" style="width:50%"> <svg width="100%"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11.76 11.76">
                            <defs>
                                <style>
                                    .cls-1 {
                                        fill: red;
                                        isolation: isolate;
                                        opacity: 0.45;
                                    }

                                    .cls-2 {
                                        fill: #818587;
                                    }

                                </style>
                            </defs>
                            <g id="Layer_2" data-name="Layer 2">
                                <g id="Layer_1-2" data-name="Layer 1">
                                    <circle class="cls-1" cx="5.88" cy="5.88" r="5.88" />
                                    <polygon points="5.89 4.61 3.73 4.61 4.73 9.32 5.89 9.32 7.06 9.32 8.06 4.61 5.89 4.61" />
                                    <path d="M8,4.13,6.61,3.44h0l.07-.14a.15.15,0,0,0-.06-.19l-.36-.18A.15.15,0,0,0,6.07,3L6,3.13v.05L4.65,2.49a.15.15,0,0,0-.19.06v.06a.14.14,0,0,0,0,.18h0L7.81,4.47A.14.14,0,0,0,8,4.41H8V4.35a.14.14,0,0,0,0-.2Z" />
                                    <path d="M3.7,4.17a1.58,1.58,0,0,1,.55-.51.85.85,0,0,1,.79,0,1.55,1.55,0,0,1,.39.28H5.09l.83.35-.4-.93V3.8a1.52,1.52,0,0,0-.6-.32.89.89,0,0,0-1,.34A1.15,1.15,0,0,0,3.7,4.17Z" />
                                    <polygon class="cls-2" points="4.41 5.12 5.41 5.12 5.41 8.8 4.96 8.8 4.31 5.12 4.41 5.12" />
                                    <polygon class="cls-2" points="7.38 5.12 6.37 5.12 6.37 8.8 6.83 8.8 7.47 5.12 7.38 5.12" />
                                    <rect class="cls-2" x="5.53" y="5.12" width="0.71" height="3.68" />
                                </g>
                            </g>
                        </svg></span>
                </a>
            </li>--}}
        </ul>
    </div>
</div>
