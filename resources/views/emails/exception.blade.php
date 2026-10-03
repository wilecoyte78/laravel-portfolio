{{-- resources/views/emails/exception.blade.php --}}
<x-mail::message>
# 🚨 {{ class_basename($data['class']) }}

**{{ $data['message'] }}**

<x-mail::panel>
**File:** `{{ $data['file'] }}:{{ $data['line'] }}`  
**URL:** {{ $data['method'] }} {{ $data['url'] }}  
**IP:** {{ $data['ip'] ?? 'n/a' }}  
**User:** {{ $data['user'] ? "#{$data['user']['id']} ({$data['user']['email']})" : 'Guest' }}  
**Time:** {{ $data['time'] }}
</x-mail::panel>

## Stack trace

```
{!! $data['trace'] !!}
```
</x-mail::message>