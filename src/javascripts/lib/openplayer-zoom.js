export default (player, zooms) => {

    let zoom = 1;
    let indicatorZoom = 1;
    let updateCanvas = false;
    let timerId;
    let pressed = false;
    const zoomChange = 0.25;
    const mouseCenter = {
        x: player.getElement().offsetWidth / 2,
        y: player.getElement().offsetHeight / 2
    };
    const videoPos = {
        x: 0,
        y: 0,
    };
    const indicatorPos = {
        x: 0,
        y: 0,
    };
    const zoomOffset = {
        x: 0,
        y: 0,
    };
    const mouseDown = {
        x: 0,
        y: 0,
    };
    const maxZoom = 8;
    const canvasDimensions = {
        width: 120,
        height: 68,
    };

    function hideZoom() {
        const container = player
            .getContainer()
            .querySelector('.zoom-container');
        if (container && container.getAttribute('aria-hidden') === 'false') {
            container.setAttribute('aria-hidden', 'true');
        }
    }

    function showZoom() {
        const container = player
            .getContainer()
            .querySelector('.zoom-container');
        if (container && container.getAttribute('aria-hidden') === 'true' && zoom > 1) {
            container.setAttribute('aria-hidden', 'false');
        }
    }

    function updateThumbs() {
        const canvas = player.getContainer().querySelector('.zoom-thumbnail');
        const ctx = canvas.getContext('2d');
        ctx.drawImage(
            player.getElement(),
            0,
            0,
            canvasDimensions.width,
            canvasDimensions.height
        );

        if (updateCanvas) {
            timerId = setTimeout(updateThumbs, 2000);
        }
    }

    function playEvent() {
        updateCanvas = true;
        updateThumbs();
    }

    function pauseEvent() {
        updateCanvas = false;
    }

    function dispatchZoomChange(action = 'increase') {
        const e = new CustomEvent('zoomchanged', {
            detail: {
                action,
            },
        });
        player.getElement().dispatchEvent(e);
    }

    function zoomIn() {
        if (zoom < maxZoom) {
            zoom += zoomChange;

            const video = player.getElement();
            const newWidth = video.offsetWidth * zoom;
            const newHeight = video.offsetHeight * zoom;
            const playerWidth = player.getContainer().offsetWidth;
            indicatorZoom = playerWidth / newWidth;

            const maxOffset = zoom - 1;
            const offset = {
                x: ((mouseCenter.x * maxOffset) / newWidth) * maxOffset,
                y: ((mouseCenter.y * maxOffset) / newHeight) * maxOffset,
            };
            offset.x = offset.x < maxOffset ? offset.x : maxOffset;
            offset.y = offset.y < maxOffset ? offset.y : maxOffset;
            zoomOffset.x = offset.x;
            zoomOffset.y = offset.y;

            video.style.transform = `scale(${zoom})`;
            video.style.transformOrigin = `${offset.x} ${offset.y}`;

            const x = zoomOffset.x / zoom;
            const y = zoomOffset.y / zoom;

            const indicator = player.getContainer().querySelector('.zoom-rect');
            indicator.style.transform = `scale(${indicatorZoom})`;
            indicator.style.transformOrigin = `${x} ${y}`;

            const container = player
                .getContainer()
                .querySelector('.zoom-container');
            if (container && container.getAttribute('aria-hidden') === 'true') {
                container.setAttribute('aria-hidden', 'false');
                updateThumbs();
            }
        }
    }

    function zoomOut() {
        if (zoom > 1) {
            zoom -= zoomChange;

            const video = player.getElement();
            const newWidth = video.offsetWidth * zoom;
            const newHeight = video.offsetHeight * zoom;
            const playerWidth = player.getContainer().offsetWidth;
            indicatorZoom = (playerWidth / newWidth);

            const maxOffset = zoom - 1;
            const offset = {
                x: ((mouseCenter.x * maxOffset) / newWidth) * maxOffset,
                y: ((mouseCenter.y * maxOffset) / newHeight) * maxOffset,
            };
            offset.x = offset.x < maxOffset ? offset.x : maxOffset;
            offset.y = offset.y < maxOffset ? offset.y : maxOffset;
            zoomOffset.x = offset.x;
            zoomOffset.y = offset.y;

            video.style.transform = `scale(${zoom})`;
            video.style.transformOrigin = `${offset.x} ${offset.y}`;

            const x = zoomOffset.x / zoom;
            const y = zoomOffset.y / zoom;

            const indicator = player.getContainer().querySelector('.zoom-rect');
            indicator.style.transform = `scale(${indicatorZoom})`;
            indicator.style.transformOrigin = `${x} ${y}`;

            const container = player
                .getContainer()
                .querySelector('.zoom-container');
            if (container && container.getAttribute('aria-hidden') === 'false' && zoom === 1) {
                container.setAttribute('aria-hidden', 'true');
            }
        }
    }

    function resetZoom() {
        zoom = 1;
        indicatorZoom = 1;

        const video = player.getElement();
        video.style.transform = `scale(${zoom})`;
        video.style.transformOrigin = '0 0';

        const indicator = player.getContainer().querySelector('.zoom-rect');
        indicator.style.transform = `scale(${indicatorZoom})`;
        indicator.style.transformOrigin = '0 0';

        const container = player
            .getContainer()
            .querySelector('.zoom-container');
        if (container) {
            container.setAttribute('aria-hidden', 'true');
        }
    }

    function handleResize() {
        if (window.outerWidth <= 500) {
            clearTimeout(timerId);
            resetZoom();
        } else if (zoom > 1) {
            const container = player
                .getContainer()
                .querySelector('.zoom-container');
            container.setAttribute('aria-hidden', 'false');
        }
    }

    function handleZoomChange(e) {
        switch (e.detail.action) {
            case 'decrease':
                zoomOut();
                break;
            case 'reset':
                resetZoom();
                break;
            default:
                zoomIn();
                break;
        }
    }

    function handleWheel(e) {
        if (!e.altKey) {
            return;
        }

        e.preventDefault();
        requestAnimationFrame(() => setTimeout(Math.sign(e.deltaY) > 0 ? zoomOut : zoomIn, 200));
    }

    function handleScreenPressed(e) {
        pressed = true;
        const layer = player.getContainer().querySelector('.zoom-layer');

        if (layer) {
            layer.setAttribute('aria-hidden', 'false');
        }

        mouseDown.x = e.offsetX;
        mouseDown.y = e.offsetY;

        e.preventDefault();
    }

    function handleScreenReleased(e) {
        pressed = false;
        const layer = player.getContainer().querySelector('.zoom-layer');

        if (layer) {
            layer.setAttribute('aria-hidden', 'true');
        }
        e.preventDefault();
    }

    function handleMovingScreen(e) {
        if (pressed) {
            const video = player.getElement();
            videoPos.x = e.offsetX;
            videoPos.y = e.offsetY;

            const mouse = {
                x: e.offsetX,
                y: e.offsetY,
            };
            const offset = {
                x: mouse.x - mouseDown.x,
                y: mouse.y - mouseDown.y
            };

            if ((Math.abs(offset.x) > 80 || Math.abs(this.y) > 80)) {
                mouseDown.x = mouse.x;
                mouseDown.y = mouse.y;
                return;
            }
            mouseDown.x = mouse.x;
            mouseDown.y = mouse.y;

            const center = {
                x: mouseCenter.x - offset.x * 1.1,
                y: mouseCenter.y - offset.y * 1.1
            };

            const maxOffset = zoom - 1;
            const o = {
                x: ((center.x * maxOffset) / video.offsetWidth) * maxOffset,
                y: ((center.y * maxOffset) / video.offsetHeight) * maxOffset,
            };

            if (o.x > maxOffset) {
                o.x = maxOffset;
            } else if (o.x < 0) {
                o.x = 0;
            } else {
                mouseCenter.x = center.x;
            }

            if (o.y > maxOffset) {
                o.y = maxOffset;
            } else if (o.y < 0) {
                o.y = 0;
            } else {
                mouseCenter.y = center.y;
            }

            video.style.transformOrigin = `${videoPos.x}px ${videoPos.y}px`;

            zoomOffset.x = o.x;
            zoomOffset.y = o.y;

            const x = videoPos.x / zoom;
            const y = videoPos.y / zoom;

            indicatorPos.x = (x * canvasDimensions.width) / (video.offsetWidth / zoom);
            indicatorPos.y = (y * canvasDimensions.height) / (video.offsetHeight / zoom);

            const indicator = player.getContainer().querySelector('.zoom-rect');
            indicator.style.transformOrigin = `${indicatorPos.x}px ${indicatorPos.y}px`;
        }
        e.preventDefault();
    }

    player.addControl({
        id: 'zoom',
        title: zooms.default.getAttribute('title'),
        position: 'right',
        icon: zooms.default.getAttribute('src'),
        subitems: [
            {
                id: 'zoom-max',
                label: '',
                title: zooms.in.getAttribute('title'),
                icon: zooms.in.getAttribute('src'),
                click: () => dispatchZoomChange('increase'),
            },
            {
                id: 'zoom-min',
                label: '',
                title: zooms.out.getAttribute('title'),
                icon: zooms.out.getAttribute('src'),
                click: () => dispatchZoomChange('decrease'),
            },
            {
                id: 'zoom-reset',
                label: '',
                title: zooms.reset.getAttribute('title'),
                icon: zooms.reset.getAttribute('src'),
                click: () => dispatchZoomChange('reset'),
            },
        ],
        init: p => {
            const container = document.createElement('div');
            container.className = 'zoom-container';
            container.setAttribute('aria-hidden', 'true');
            container.innerHTML = `
                <canvas width='${canvasDimensions.width}' height='${canvasDimensions.height}' class='zoom-thumbnail'></canvas>
                <div class='zoom-rect' />
            `;
            p.getContainer().appendChild(container);

            const layer = document.createElement('div');
            layer.className = 'zoom-layer';
            layer.setAttribute('aria-hidden', 'true');
            p.getContainer().appendChild(layer);

            const zoomInBtn = document.createElement('button');
            zoomInBtn.append(zooms.in_overlay);
            zoomInBtn.className = 'op-zoom op-zoom__in';
            zoomInBtn.setAttribute('aria-label', zooms.in_overlay.getAttribute('title'));
            zoomInBtn.addEventListener('click', () => dispatchZoomChange('increase'), { passive: false });

            const zoomOutBtn = document.createElement('button');
            zoomOutBtn.append(zooms.out_overlay);
            zoomOutBtn.className = 'op-zoom op-zoom__out';
            zoomOutBtn.setAttribute('aria-label', zooms.out_overlay.getAttribute('title'));
            zoomOutBtn.addEventListener('click', () => dispatchZoomChange('decrease'), { passive: false });

            p.getContainer().appendChild(zoomOutBtn);
            p.getContainer().appendChild(zoomInBtn);

            const video = p.getElement();
            video.addEventListener('play', playEvent, { passive: false });
            video.addEventListener('pause', pauseEvent, { passive: false });
            video.addEventListener('zoomchanged', handleZoomChange, { passive: false });
            video.addEventListener('mouseenter', showZoom, { passive: false });
            video.addEventListener('mousemove', showZoom, { passive: false });
            video.addEventListener('mousedown', handleScreenPressed, { passive: false });
            video.addEventListener('controlshidden', hideZoom, { passive: false });
            video.addEventListener('wheel', handleWheel, { passive: false });

            layer.addEventListener('mousemove', handleMovingScreen, { passive: false });
            layer.addEventListener('mouseup', handleScreenReleased, { passive: false });
            layer.addEventListener('mouseleave', handleScreenReleased, { passive: false });
            window.addEventListener('resize', handleResize, { passive: false });
        },
        click: () => {
            const button = player
                .getControls()
                .getContainer()
                .querySelector('#zoom');

            if (button.classList.contains('selected')) {
                button.classList.remove('selected');
            } else {
                button.classList.add('selected');
            }
        },
        destroy: p => {
            const button = p.getControls().getContainer().querySelector('#zoom');

            clearTimeout(timerId);

            const video = p.getElement();
            video.removeEventListener('play', playEvent);
            video.removeEventListener('pause', pauseEvent);
            video.removeEventListener('zoomchanged', handleZoomChange);
            video.removeEventListener('mousedown', handleScreenReleased);
            video.removeEventListener('mouseenter', showZoom);
            video.removeEventListener('mousemove', showZoom);
            video.removeEventListener('controlshidden', hideZoom);
            video.removeEventListener('wheel', handleWheel);

            const layer = player.getContainer().querySelector('.zoom-layer');
            layer.removeEventListener('mousemove', handleMovingScreen);
            layer.removeEventListener('mouseup', handleScreenReleased);
            layer.removeEventListener('mouseleave', handleScreenReleased);
            window.removeEventListener('resize', handleResize);
            button.remove();

            const zoomInBtn = player.getContainer().querySelector('.op-zoom__in');
            zoomInBtn.removeEventListener('click', () => dispatchZoomChange('increase'));
            zoomInBtn.remove();

            const zoomOutBtn = player.getContainer().querySelector('.op-zoom__out');
            zoomOutBtn.removeEventListener('click', () => dispatchZoomChange('decrease'));
            zoomOutBtn.remove();
        },
    });
}