import { toast } from 'sonner';

export default function Test() {
    return (<><button onClick={() => toast.error('test')}>show</button></>)
}
