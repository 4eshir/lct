// Задаем цвета и соответствующие оттенки
const colors = [
    { color: 'rgba(255, 0, 0, 0.5)', borderColor: 'rgb(200, 0, 0)' },   // Красный
    { color: 'rgba(4, 183, 11, 0.5)', borderColor: 'rgb(4, 183, 11)' },   // Зеленый
    { color: 'rgba(0, 0, 255, 0.5)', borderColor: 'rgb(0, 0, 200)' }    // Синий
];

// Пример точек
let points = [
   /* { x: 10, y: 10, colorIndex: 0 },
    { x: 100, y: 100, colorIndex: 0 },
    { x: 10, y: 100, colorIndex: 0 },
    { x: 50, y: 70, colorIndex: 0 },
    { x: 50, y: 60, colorIndex: 0 },
    { x: 300, y: 300, colorIndex: 0 },
    { x: 500, y: 400, colorIndex: 0 },
    { x: 300, y: 500, colorIndex: 0 },
    { x: 350, y: 450, colorIndex: 0 },
    { x: 400, y: 20, colorIndex: 1 },
    { x: 500, y: 40, colorIndex: 1 },
    { x: 350, y: 100, colorIndex: 1 },
    { x: 250, y: 100, colorIndex: 1 },
    { x: 240, y: 50, colorIndex: 1 },
    { x: 300, y: 70, colorIndex: 1 },
    { x: 200, y: 200, colorIndex: 2 },
    { x: 200, y: 250, colorIndex: 2 },
    { x: 250, y: 250, colorIndex: 2 },
    { x: 250, y: 200, colorIndex: 2 },
    { x: 220, y: 220, colorIndex: 2 },*/
];

function addPoint(x, y, colorIndex) {
    points.push({x: x, y: y, colorIndex: colorIndex});
}

// Максимальное расстояние между точками в одном кластере
const maxDistance = 200;

// Функция для вычисления расстояния между двумя точками
function distance(p1, p2) {
    return Math.sqrt((p1.x - p2.x) ** 2 + (p1.y - p2.y) ** 2);
}

function clearCanvas() {
    // Очищаем канвас
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Восстанавливаем фоновое изображение
    ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
    points = [];
}

// Функция для группировки точек в кластеры
function clusterPoints(points) {
    const clusters = [];

    points.forEach(point => {
        let added = false;

        for (const cluster of clusters) {
            if (cluster[0].colorIndex === point.colorIndex && 
                cluster.some(existingPoint => distance(existingPoint, point) <= maxDistance)) {
                cluster.push(point);
                added = true;
                break;
            }
        }

        if (!added) {
            clusters.push([point]);
        }
    });

    return clusters;
}

// Функция для нахождения выпуклой оболочки (алгоритм Грэмма)
function convexHull(points) {
    points = points.slice(); // Копируем точки, чтобы не изменять исходный массив
    points.sort((a, b) => a.x - b.x || a.y - b.y); // Сортировка по x и y

    const buildHull = (points) => {
        const hull = [];
        
        for (const point of points) {
            while (hull.length >= 2 &&
                cross(hull[hull.length - 2], hull[hull.length - 1], point) <= 0) {
                hull.pop();
            }
            
            hull.push(point);
        }
        return hull;
    };

    const lower1 = buildHull(points);
    const upper1 = buildHull(points.reverse());

    upper1.pop(); // Убираем последнюю точку, чтобы не дублировать

    const preparePointsTemp = lower1.concat(upper1);
    const preparePoints = [];

    // Используем Set для отслеживания уникальных строковых представлений объектов
    const uniquePointsSet = new Set();

    for (const point of preparePointsTemp) {
        const pointString = JSON.stringify(point); // Преобразуем объект в строку для использования в Set

        if (!uniquePointsSet.has(pointString)) {
            uniquePointsSet.add(pointString); // Добавляем уникальную строку
            preparePoints.push(point); // Добавляем объект в результирующий массив
        }
    }

    hullOffset = [];

    for (let i = 0; i < preparePoints.length; i++) {
        if (i != 0 && i != preparePoints.length - 1) {
            hullOffset.push(offsetByVectors(preparePoints[i], preparePoints[i - 1], preparePoints[i + 1], 30));
        }
        else if (i != 0) {
            hullOffset.push(offsetByVectors(preparePoints[i], preparePoints[i - 1], preparePoints[0], 30));
        }
        else if (i != preparePoints.length - 1) {
            hullOffset.push(offsetByVectors(preparePoints[i], preparePoints[preparePoints.length - 1], preparePoints[i + 1], 30));
        }
    }

    console.log(hullOffset);

    return hullOffset;
}

// Векторное произведение для определения ориентации
function cross(o, a, b) {
    return (a.x - o.x) * (b.y - o.y) - (a.y - o.y) * (b.x - o.x);
}

function offsetByVectors(curPoint, point1, point2, range) {
    const vector1 = {
        x: point1.x - curPoint.x,
        y: point1.y - curPoint.y,
    };
    
    const vector2 = {
        x: point2.x - curPoint.x,
        y: point2.y - curPoint.y,
    };
    
    // Суммируем векторы
    const summedVector = {
        x: vector1.x + vector2.x,
        y: vector1.y + vector2.y,
    };
    
    // Нормализуем вектор
    const magnitude = Math.sqrt(summedVector.x ** 2 + summedVector.y ** 2);
    
    if (magnitude === 0) {
        // Если вектор нулевой, возвращаем curPoint (или можете обработать по-другому)
        return { ...curPoint };
    }

    const normalizedVector = {
        x: summedVector.x / magnitude,
        y: summedVector.y / magnitude,
    };
    
    // Получаем новую точку на расстоянии range
    const newPoint = {
        x: curPoint.x - normalizedVector.x * range,
        y: curPoint.y - normalizedVector.y * range,
    };
    
    return newPoint;
}


function drawClusters() {
    const clusters = clusterPoints(points);

// Рисование точек
    points.forEach(point => {
        // Рисуем белую обводку
        ctx.strokeStyle = 'white'; // Устанавливаем цвет обводки в белый
        ctx.lineWidth = 3; // Устанавливаем ширину линии для белой обводки
        ctx.beginPath();
        ctx.arc(point.x, point.y, 3, 0, Math.PI * 2);
        ctx.stroke(); // Рисуем белую обводку

        // Рисуем цветную обводку
        ctx.strokeStyle = colors[point.colorIndex].borderColor; // Устанавливаем цвет соответствующего кластера
        ctx.lineWidth = 2; // Устанавливаем тонкую обводку
        ctx.beginPath();
        ctx.arc(point.x, point.y, 6, 0, Math.PI * 2); // Увеличиваем радиус
        ctx.stroke(); // Рисуем обводку соответствующего цвета

        // Рисуем саму точку
        ctx.fillStyle = colors[point.colorIndex].borderColor; // Устанавливаем цвет заливки
        ctx.beginPath();
        ctx.arc(point.x, point.y, 4, 0, Math.PI * 2); // Уменьшаем радиус точки
        ctx.fill(); // Заполняем точку
    });

// Рисование областей кластеров
    clusters.forEach(cluster => {
        if (cluster.length === 0) return;

        const colorIndex = cluster[0].colorIndex; // Цвет кластера
        ctx.fillStyle = colors[colorIndex].color; // Полупрозрачный цвет

        const hull = convexHull(cluster);

        ctx.beginPath();

        if (hull.length > 0) {
            ctx.moveTo(hull[0].x, hull[0].y);

            for (let i = 1; i < hull.length; i++) {
                const nextPoint = hull[i];
                const prevPoint = i == 0 ? hull[hull.length - 1] : hull[i - 1];
                const radius = 10; // Радиус скругления углов
                ctx.arcTo(prevPoint.x, prevPoint.y, nextPoint.x, nextPoint.y, radius);
            }

            ctx.arcTo(hull[hull.length - 1].x, hull[hull.length - 1].y, hull[0].x, hull[0].y, 10);
            ctx.arcTo(hull[0].x, hull[0].y, hull[1].x, hull[1].y, 10);

            ctx.closePath();
            ctx.fill();
        }

        // Рисуем границы
        ctx.strokeStyle = 'rgba(0, 0, 0, 0)'; // Непрозрачный цвет для границ
        ctx.lineWidth = 1; // Ширина линии
        ctx.stroke();
    });
}