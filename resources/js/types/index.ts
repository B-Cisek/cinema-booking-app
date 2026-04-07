export type * from './auth';

export interface Cinema {
    id: string;
    street: string;
    city: string;
}

export interface ScheduleDay {
    date: string;
    label: string;
    relative_label: string;
}

export interface ScreeningMovie {
    title: string;
    description: string;
    duration: number;
    poster_url: string;
}

export interface ScreeningHall {
    label: string;
}

export interface Screening {
    id: string;
    date: string;
    starts_at: string;
    ends_at: string;
    status: string;
    hall: ScreeningHall;
    movie: ScreeningMovie;
}

export type SeatType = 'standard' | 'vip' | 'wheelchair' | 'couple';
export type Row = 'A' | 'B' | 'C' | 'D' | 'E' | 'F' | 'G' | 'H' | 'I' | 'J' | 'K' | 'L';

export interface Seat {
    id: string;
    row: Row;
    seatNumber: number;
    seatType: SeatType;
    posX: number;
    posY: number;
    isActive: boolean;
    isBooked: boolean;
}

export interface HallRow {
    label: Row;
    seats: Array<Seat | null>;
}
