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

export interface SeatMapSeat {
    id: string;
    row: string;
    seatNumber: number;
    seatType: SeatType;
    posX: number;
    posY: number;
    isActive: boolean;
    isBooked: boolean;
}

export interface SeatMapRow {
    label: string;
    seats: Array<SeatMapSeat | null>;
}
