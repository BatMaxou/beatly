export const convertDurationInMinutes = (duration: string | number) => {
  console.warn("convertDurationInMinutes est dépréciée. Remplacé par useTimeFormat().");
  const durationNumber = typeof duration === "string" ? parseInt(duration, 10) : duration;

  if (isNaN(durationNumber)) {
    return "0:00";
  }

  const minutes = Math.floor(durationNumber / 60);
  const seconds = durationNumber % 60;
  return `${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;
};