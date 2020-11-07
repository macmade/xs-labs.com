<h2>Binary representation of single precision floating point numbers</h2>
<div class="row">
    <div class="col-md-10">
        <p>
            <dl class="dl-horizontal">
                <dt>Author</dt>
                <dd>
                    Jean-David Gadina
                </dd>
                <dt>Copyright</dt>
                <dd>
                    &copy; <?php print date( 'Y', time() ); ?> Jean-David Gadina - www.xs-labs.com - All Rights Reserved
                </dd>
                <dt>License</dt>
                <dd>
                    This article is published under the terms of the <?php print  \XS\Menu::getInstance()->getPageLink( '/licenses/freebsd-documentation' ); ?>
                </dd>
                <dt>Source</dt>
                <dd>
                    <a href="http://ieeexplore.ieee.org/servlet/opac?punumber=4610933" title="IEEE 754">IEEE Standard for Floating-Point Arithmetic - IEEE 754</a>
                </dd>
            </dl>
        </p>
        <a name="theory"></a>
        <h3>1. Theory</h3>
        <p>
            Single precsion floating point numbers are usually called 'float', or 'real'. They are 4 bytes long, and are packed the following way, from left to right:
        </p>
        <p>
            <dl class="dl-horizontal">
                <dt>Sign</dt>
                <dd>1 bit</dd>
                <dt>Exponent</dt>
                <dd>8 bits</dd>
                <dt>Mantissa</dt>
                <dd>23 bits</dd>
            </dl>
        </p>
        <table class="table table-responsive table-bordered">
            <tr class="active">
                <td>X</td>
                <td>XXXX XXXX</td>
                <td>XXX XXXX XXXX XXXX XXXX XXXXX</td>
            </tr>
            <tr>
                <th>Sign<br />1 bit</th>
                <th>Exponent<br />8 bits</th>
                <th>Mantissa<br />23 bits</th>
            </tr>
        </table>
        <p>
            The sign indicates if the number is positive or negative (zero for positive, one for negative).
        </p>
        <p>
            The real exponent is computed by substracting 127 to the value of the exponent field. It's the exponent of the number as it is expressed in the scientific notation.
        </p>
        <p>
            The full mantissa, which is also sometimes called significand, should be considered as a 24 bits value. As we are using scientific notation, there is an implicit leading bit (sometimes called the hidden bit), always set to 1, as there is never a leading 0 in the scientific notation.<br />
            For instance, you won't say <code>0.123 &middot; 10<span class="power">5</span></code> but <code>1.23 &middot; 10<span class="power">4</span></code>.
        </p>
        <p>
            The conversion is performed the following way:
        </p>
<pre>
-1S &middot; 1.M &middot; 2( E - 127 )
</pre>
        <p>
            Where S is the sign, M the mantissa, and E the exponent.
        </p>
        <a name="example"></a>
        <h3>2. Example</h3>
        <p>
            For instance, <code>0100 0000 1011 1000 0000 0000 0000 0000</code>, which is <code>0x40B80000</code> in hexadecimal.
        </p>
        <table class="table table-responsive table-bordered">
            <tr>
                <th class="active">Hex</th>
                <td>4</td>
                <td class="active">0</td>
                <td>B</td>
                <td class="active">8</td>
                <td>0</td>
                <td class="active">0</td>
                <td>0</td>
                <td class="active">0</td>
            </tr>
            <tr>
                <th class="active">Bin</th>
                <td>0100</td>
                <td class="active">0000</td>
                <td>1011</td>
                <td class="active">1000</td>
                <td>0000</td>
                <td class="active">0000</td>
                <td>0000</td>
                <td class="active">0000</td>
            </tr>
        </table>
        <table class="table table-responsive table-bordered">
            <tr>
                <th class="active">Sign</th>
                <th class="active">Exponent</th>
                <th class="active">Mantissa</th>
            </tr>
            <tr>
                <td>0</td>
                <td>1000 0001</td>
                <td>(1) 011 1000 0000 0000 0000 0000</td>
            </tr>
        </table>
        <ul>
            <li>The sign is <code>0</code>, so the number is positive.</li>
            <li>The exponent field is <code>1000 0001</code>, which is 129 in decimal. The real exponent value is then 129 - 127, which is 2.</li>
            <li>The mantissa with the leading 1 bit, is <code>1011 1000 0000 0000 0000 0000</code>.</li>
        </ul>
        <p>
            The final representation of the number in the binary scientific notation is:
        </p>
<pre>
-10 &middot; 1.0111 &middot; 22
</pre>
        <p>
            Mathematically, this means:
        </p>
<pre>
1 &middot; ( 1 &middot; 20 + 0 &middot; 2-1 + 1 &middot; 2-2 + 1 &middot; 2-3 + 1 &middot; 2-4 ) &middot; 22
( 20 + 2-2 + 2-3 + 2-4 ) &middot; 22
22 + 20 + 2-1 + 2-2
4 + 1 + 0.5 + 0.25
</pre>
        <p>
            The floating point value is then 5.75.
        </p>
        <a name="special-numbers"></a>
        <h3>3. Special numbers</h3>
        <p>
            Depending on the value of the exponent field, some numbers can have special values. They can be:
        </p>
        <ul>
            <li>Denormalized numbers</li>
            <li>Zero</li>
            <li>Infinity</li>
            <li>NaN (not a number)</li>
        </ul>
        <a name="special-numbers-denormalized"></a>
        <h4>3.1. Denormalized numbers</h4>
        <p>
            If the value of the exponent field is 0 and the value of the mantissa field is greater than 0, then the number has to be treated as a denormalized number.<br />
            In such a case, the exponent is not -127, but -126, and the implicit leading bit is not 1 but 0.<br />
            That allows smaller numbers to be represented.
        </p>
        <p>
            The scientific notation for a denormalized number is:
        </p>
<pre>
-1S &middot;  0.M &middot; 2-126
</pre>
        <a name="special-numbers-zero"></a>
        <h4>3.2. Zero</h4>
        <p>
            If the exponent and the mantissa fields are both 0, then the final number is zero. The sign bit is permitted, even if it does not have much sense mathematically, allowing a positive or a negative zero.<br />
            Note that zero can be considered as a denormalized number. In that case, it would be <code>0 &middot; 2<span class="power">-126</span></code>, which is zero.
        </p>
        <a name="special-numbers-infinity"></a>
        <h4>3.3. Infinity</h4>
        <p>
            If the value of the exponent field is 255 (all 8 bits are set) and if the value of the mantissa field is 0, the number is an infinity, either positive or negative, depending on the sign bit.
        </p>
        <a name="special-numbers-nan"></a>
        <h4>3.4. NaN</h4>
        <p>
            If the value of the exponent field is 255 (all 8 bits are set) and if the value of the mantissa field is not 0, then the value is not a number. The sign bit as no meaning in such a case.
        </p>
        <a name="range"></a>
        <h3>3. Range</h3>
        <p>
            The range depends if the number is normalized or not. Below are the ranges for that two cases:
        </p>
        <a name="range-normalized"></a>
        <h4>3.1 Normalized numbers</h4>
        <ul>
            <li><strong>Min:</strong> <code>±1.1754944909521E-38</code> / <code>±1.00000000000000000000001<span class="power">-126</span></code></li>
            <li><strong>Max:</strong> <code>±3.4028234663853E+38</code> / <code>±1.11111111111111111111111<span class="power">128</span></code></li>
        </ul>
        <a name="range-denormalized"></a>
        <h4>3.2 Denormalized numbers</h4>
        <ul>
            <li><strong>Min:</strong> <code>±1.4012984643248E-45</code> / <code>±0.00000000000000000000001<span class="power">-126</span></code></li>
            <li><strong>Max:</strong> <code>±1.1754942106924E-38</code> / <code>±0.11111111111111111111111<span class="power">-126</span></code></li>
        </ul>
        <a name="c-code"></a>
        <h3>4. C code example</h3>
        <p>
            Below is an example of a C program that will converts a binary number to its float representation:
        </p>
        <div class="code-block language-c">
#include &lt;stdlib.h&gt;
#include &lt;stdio.h&gt;
#include &lt;math.h&gt;

/**
 * Converts a integer to its float representation
 * 
 * This function converts a 32 bits integer to a single precision floating point
 * number, as specified by the IEEE Standard for Floating-Point Arithmetic
 * (IEEE 754). This standard can be found at the folowing address:
 * {@link http://ieeexplore.ieee.org/servlet/opac?punumber=4610933}
 * 
 * @param   unsigned long   The integer to convert to a floating point value
 * @return  The floating point number
 */
float binaryToFloat( unsigned int binary );
float binaryToFloat( unsigned int binary )
{
    unsigned int sign;
    int          exp;
    unsigned int mantissa;
    float        floatValue;
    int          i;
    
    /* Gets the sign field */
    /* Bit 0, left to right */
    sign = binary >> 31;
    
    /* Gets the exponent field */
    /* Bits 1 to 8, left to right */
    exp = ( ( binary >> 23 ) & 0xFF );
    
    /* Gets the mantissa field */
    /* Bits 9 to 32, left to right */
    mantissa = ( binary & 0x7FFFFF );
    
    floatValue  = 0;
    i           = 0;
    
    /* Checks the values of the exponent and the mantissa fields to handle special numbers */
    if( exp == 0 && mantissa == 0 )
    {
        /* Zero - No need for a computation even if it can be considered as a denormalized number */
        return 0;
    }
    else if( exp == 255 && mantissa == 0 )
    {
        /* Infinity */
        return 0;
    }
    else if( exp == 255 && mantissa != 0 )
    {
        /* Not a number */
        return 0;
    }
    else if( exp == 0 && mantissa != 0 )
    {
        /* Denormalized number - Exponent is fixed to -126 */
        exp = -126;
    }
    else
    {
        /* Computes the real exponent */
        exp = exp - 127;
    
        /* Adds the implicit bit to the mantissa */
        mantissa = mantissa | 0x800000;
    }
    
    /* Process the 24 bits of the mantissa */
    for( i = 0; i > -24; i-- )
    {
        /* Checks if the current bit is set */
        if( mantissa & ( 1 << ( i + 23 ) ) )
        {
            /* Adds the value for the current bit */
            /* This is done by computing two raised to the power of the exponent plus the bit position */
            /* (negative if it's after the implicit bit, as we are using scientific notation) */
            floatValue += ( float )pow( 2, i + exp );
        }
    }
    
    /* Returns the final float value */
    return ( sign == 0 ) ? floatValue : -floatValue;
}

int main( void )
{
    printf( "%f\n", binaryToFloat( 0x40B80000 ) );
    
    return EXIT_SUCCESS;
}
        </div>
    </div>
    <div class="col-md-2 resume-menu">
        <div data-spy="affix" data-offset-top="0" data-offset-bottom="0" class="resume-menu-affix">
            <ul class="hidden-xs">
                <li>
                    <a href="#theory" title="Go to this section"><strong>Theory</strong></a>
                </li>
                <li>
                    <a href="#example" title="Go to this section"><strong>Example</strong></a>
                </li>
                <li>
                    <a href="#special-numbers" title="Go to this section"><strong>Special numbers</strong></a>
                    <ul>
                        <li>
                            <a href="#special-numbers-denormalized" title="Go to this section">Denormalized numbers</a>
                        </li>
                        <li>
                            <a href="#special-numbers-zero" title="Go to this section">Zero</a>
                        </li>
                        <li>
                            <a href="#special-numbers-infinity" title="Go to this section">Infinity</a>
                        </li>
                        <li>
                            <a href="#special-numbers-nan" title="Go to this section">NaN</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#range" title="Go to this section"><strong>Range</strong></a>
                    <ul>
                        <li>
                            <a href="#range-normalized" title="Go to this section">Normalized numbers</a>
                        </li>
                        <li>
                            <a href="#range-denormalized" title="Go to this section">Denormalized numbers</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#c-code" title="Go to this section"><strong>C code example</strong></a>
                </li>
            </ul>
        </div>
    </div>
</div>

