<div class="form-group">
    <label for="name">Name: </label>
    <input type="text" value="{{old('name') ? old('name') : auth()->user()->name}}" name="name" required />
</div>
@error("name")
<span class="err-msg">{{$message}}</span>
            @enderror
            <div class="form-group">
                <label for="phone_number">Phone Number: </label>
                <input type="number" value="{{old('phone_number')}}" name="phone_number" required />
            </div>
            @error("phone_number")
            <span class="err-msg">{{$message}}</span>
        @enderror
        @if(count($countries) > 0)
            <div class="form-group">
                <label for="country">Country: </label>
                <select
                    name="country"
                    value="{{old('country_id')}}"
                    id="country_id"
                    data-url="{{$url}}"
                    >
                    @foreach($countries as $country)
                        <option value="{{$country->id}}">{{$country->name}}</option>
                    @endforeach
                </select>
            </div>
        @else
            <h4>There are some trouble in get country. please, try after sometime</h4>
        @endif
        @error("country")
        <span class="err-msg">{{$message}}</span>
    @enderror
    @if(count($states) > 0)
        <div class="form-group">
            <label for="state">State: </label>
            <select
                name="state"
                value="{{old('state_id')}}"
                id="state_id"
                data-url="{{$url}}"
                >
                @foreach($states as $state)
                    <option value="{{$state->id}}">{{$state->name}}</option>
                @endforeach
            </select>
        </div>
    @else
        <h4>There are some trouble in get state. please, try after sometime</h4>
    @endif
    @error("state")
    <span class="err-msg">{{$message}}</span>
@enderror
<div class="form-group">
    <label for="district">District: </label>
    <select name="district" value="{{old('district_id')}}" id="district_id">
    </select>
</div>
@error("district")
<span class="err-msg">{{$message}}</span>
            @enderror
            <div class="form-group">
                <label for="house_number">House Number: </label>
                <input type="number" value="{{old('house_number')}}" name="house_number" required />
            </div>
            @error("house_number")
            <span class="err-msg">{{$message}}</span>
        @enderror
        <div class="form-group">
            <label for="city">City: </label>
            <input type="text" value="{{old('city')}}" name="city" required />
        </div>
        @error("city")
        <span class="err-msg">{{$message}}</span>
    @enderror
    <div class="form-group">
        <label for="pin_code">Pin Code: </label>
        <input type="number" value="{{old('pin_code')}}" name="pin_code" required />
    </div>
    @error("pin_code")
    <span class="err-msg">{{$message}}</span>
@enderror
<div class="form-group">
    <label for="address">Address: </label>
    <textarea
        name="address"
        value="{{old('address')}}"
        class="
        field-sizing-content
        overflow-hidden
        min-h-[2.5rem]
        w-full
        ">
    </textarea>
</div>
@error("address")
<span class="err-msg">{{$message}}</span>
            @enderror
