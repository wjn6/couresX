registerPaint(
    "waveDraw",
    class {
        static get inputProperties() {
            return [
                "--animation-tick", 
                "--height", 
                "--gap",
                "--amplitude",
                "--color1",
                "--color2",
                "--color3"
            ];
        }
        
        paint(ctx, size, properties) {
            const tick = parseFloat(properties.get("--animation-tick"));
            const initHeight = parseFloat(properties.get("--height"));
            const gap = parseFloat(properties.get("--gap"));
            const amplitude = parseFloat(properties.get("--amplitude"));
            const color1 = properties.get("--color1").toString();
            const color2 = properties.get("--color2").toString();
            const color3 = properties.get("--color3").toString();
            
            // Draw waves
            this.drawWave(ctx, size, tick, amplitude, gap, initHeight, color1);
            this.drawWave(ctx, size, tick * 1.21, amplitude / 0.82, gap + 2, initHeight + 0.02, color2);
            this.drawWave(ctx, size, tick * 0.79, amplitude / 1.19, gap - 2, initHeight - 0.02, color3);
        }
        
        drawWave(ctx, size, tick, amplitude, gap, initHeight, color) {
            const { width, height } = size;
            const initY = height * initHeight;
            tick *= 2;
            
            ctx.beginPath();
            for (let i = 0; i <= width; i++) {
                ctx.lineTo(i, initY + Math.sin((i + tick) / gap) * amplitude);
            }
            ctx.lineTo(width, height);
            ctx.lineTo(0, height);
            ctx.lineTo(0, initY);
            ctx.closePath();
            ctx.fillStyle = color;
            ctx.fill();
        }
    }
);
