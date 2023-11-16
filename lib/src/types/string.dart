import 'dart:core' hide String;
import 'dart:core' as core show String;

import '../core/ovo.dart';
import '../functional/refine.dart';
import '../functional/transform.dart';

typedef String = OvO<core.String>;

extension OvOString on String {
  String min(int length, {core.String? message}) {
    return refine(
      (data) => data.length >= length,
      message: message ?? 'Minimum length is $length',
    );
  }

  String max(int length, {core.String? message}) {
    return refine(
      (data) => data.length <= length,
      message: message ?? 'Maximum length is $length',
    );
  }

  String length(int length, {core.String? message}) {
    return refine(
      (data) => data.length == length,
      message: message ?? 'Length must be $length',
    );
  }

  String regex(RegExp regex, {core.String? message}) {
    return refine(
      (data) => regex.hasMatch(data),
      message: message ?? 'Must match pattern $regex',
    );
  }

  String contains(Pattern value, {core.String? message}) {
    return refine(
      (data) => data.contains(value),
      message: message ?? 'Must contain $value',
    );
  }

  String isNotEmpty({core.String? message}) {
    return refine(
      (data) => data.isNotEmpty,
      message: message ?? 'Must not be empty',
    );
  }

  String startsWith(Pattern value, {core.String? message}) {
    return refine(
      (data) => data.startsWith(value),
      message: message ?? 'Must start with $value',
    );
  }

  String endsWith(core.String value, {core.String? message}) {
    return refine(
      (data) => data.endsWith(value),
      message: message ?? 'Must end with $value',
    );
  }

  String equals(core.String value, {core.String? message}) {
    return refine(
      (data) => data == value,
      message: message ?? 'Must equal $value',
    );
  }

  String trim() {
    return transform((_, data, next) async {
      final result = await next(data);

      return result.trim();
    });
  }

  String toLowerCase() {
    return transform((_, data, next) async {
      final result = await next(data);

      return result.toLowerCase();
    });
  }

  String toUpperCase() {
    return transform((_, data, next) async {
      final result = await next(data);

      return result.toUpperCase();
    });
  }
}
