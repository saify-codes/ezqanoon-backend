// --- inputs -------------------------------------------------------------
const day  = 'sat';        // any of: sun, mon, tue, wed, thu, fri, sat  (case-insensitive)
const time = '01:00 PM';   // 12-hour clock, “HH:MM AM/PM”
// -----------------------------------------------------------------------

// helper: map weekday string → numeric day-of-week (Sun = 0 … Sat = 6)
const WEEKDAY = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
const targetDow = WEEKDAY.indexOf(day.toLowerCase());
if (targetDow === -1) throw new Error(`Invalid weekday: ${day}`);

// current moment (local time zone)
const now = new Date();

// start with a copy of “now”, then set the clock to the requested time
const next = new Date(now);

// --- parse the “10:00 PM” bit ------------------------------------------
const [clock, meridian] = time.trim().split(/\s+/);     // ["10:00", "PM"]
let   [hrs, mins]      = clock.split(':').map(Number);  // [10, 0]

if (meridian.toLowerCase() === 'pm' && hrs !== 12) hrs += 12;
if (meridian.toLowerCase() === 'am' && hrs === 12) hrs  = 0;

// put that time into the `next` Date object
next.setHours(hrs, mins, 0, 0);   // sets h, m, s, ms

// --- bump to the right calendar day ------------------------------------
// diff between target weekday and the (possibly adjusted-for-time) today
let diffDays = (targetDow - next.getDay() + 7) % 7

console.log(diffDays);


// if today *is* the target weekday but the clock time has passed,
// push it one full week ahead
if (diffDays === 0 && next <= now) diffDays = 7;

next.setDate(next.getDate() + diffDays);

// -----------------------------------------------------------------------
// `next` is the Date object representing the *next* <day> at <time>
console.log(`Next ${day} at ${time} is:`, next.toString());


// let a = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat']
// let d = new Date()
// d.setDate(1)

// console.log(d.getDay());

