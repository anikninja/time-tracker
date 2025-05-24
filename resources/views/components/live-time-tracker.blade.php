<div
    x-data="liveTimeTracker({{ $initialTime }}, {{ $isTracking ? 'true' : 'false' }})"
    x-init="startTimer()"
>
    <span x-text="formattedTime"></span>
</div>

<script>
    function liveTimeTracker(initialTime, isTracking) {
        return {
            elapsedTime: initialTime,
            formattedTime: '',
            isTracking: isTracking,
            startTimer() {
                this.updateFormattedTime();
                if (this.isTracking) {
                    setInterval(() => {
                        this.elapsedTime++;
                        this.updateFormattedTime();
                    }, 1000);
                }
            },
            updateFormattedTime() {
                const date = new Date(this.elapsedTime * 1000);
                this.formattedTime = date.toISOString().substr(11, 8);
            }
        };
    }
</script>
